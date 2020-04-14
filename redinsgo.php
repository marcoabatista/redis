<?php

$redis = new Redis(); 
$redis->connect('127.0.0.1', 6379); 

$redis->flushall();

echo "Aplicativo de bingo - REDISGO"; 
echo "\n";
echo "Resposta do servidor REDIS ao PING: ".$redis->ping();
echo "\n";

//montagem do conjunto das cartelas
for ($i=1; $i <= 99; $i++) {
    $redis->sadd("cartela","$i");
}

$totreg = 5; // total de registros

for ($i=1; $i <= $totreg; $i++) {
    $id = "user:$i";    
    $data = [];
    $data["name"] = "usuario$i";
    $data["bcartela"] = "cartela:$i";
    $data["bscore"] = "score:$i";

    $redis->hmset($id,$data);
    
    $redis->spop("cartela:$i",15);
    $numeros=$redis->SRANDMEMBER("cartela","20");   
    $qtdnum=0;
    foreach ($numeros as $k => $v) {
        if (empty($redis->sismember("cartela:$i","$v"))) {
            $redis->sadd("cartela:$i","$v");            
            $qtdnum++;
        }
        if ($qtdnum>=15) {
            break;
        }        
    }    
    
    $redis->zadd("score","0","score:$i");

    $cartela=implode(",",$redis->smembers("cartela:$i"));
    echo $data["name"]." - Cartela: $cartela\n";
}

$redis->spop("sorteio",99);
$indsor=true;
$useven="";
$qtdnum=0;
while ($indsor) {
    $numsor = $redis->SRANDMEMBER("cartela","1");
    $numsor = $numsor[0];
    
    if (empty($redis->sismember("sorteio",$numsor))) {
        $redis->sadd("sorteio",$numsor);
        $qtdnum++;

        for ($i=1; $i <= $totreg; $i++) {                        
            if ($redis->sismember("cartela:$i",$numsor)==1) {                                
                $redis->zincrby("score","1","score:$i");                         
            }
            
            if ($redis->zscore("score","score:$i")>=15) {
                $useven="user:$i";
                $indsor=false;
                break;
            }
        }        
    }        
    
    if ($qtdnum>=99) {
        $indsor=false;            
    } 
}

$sorteio=implode(",",$redis->smembers("sorteio"));
echo "Pedras sorteadas: $sorteio\n";
echo "Qtde. pedras sorteadas: $qtdnum\n";

for ($i=1; $i <= $totreg; $i++) {
    echo $redis->hget("user:$i","name")." - Acertos: ".$redis->zscore("score","score:$i")."\n";                
}        

echo "Vencedor: ".$redis->hget($useven,"name")."\n";
?>