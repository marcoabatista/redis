# redis

<?php

$redis = new Redis(); 
$redis->connect('127.0.0.1', 6379); 

//$redis->flushall();

echo "REDISGO"; 
echo "\n";
echo "Resposta do servidor REDIS ao PING: ".$redis->ping();
echo "\n";

//montage do conjunto das cartelas
for ($i=1; $i <= 99; $i++) {
    $redis->sadd("cartela","$i");
}

$totreg = 2; // total de registros

for ($i=1; $i <= $totreg; $i++) {
    $user = readline("Usuário: ");
    $redis->set("user:$i","name:$user,bcartela:cartela$i,bscore:score$i");
    
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
    
    $redis->zadd("score","0","score$i");

    $cartela=implode(",",$redis->smembers("cartela:$i"));
    echo "Cartela: $cartela\n";
}

$redis->spop("sorteio",15);
$numeros=$redis->SRANDMEMBER("cartela","20");   
$qtdnum=0;
foreach ($numeros as $k => $v) {
    if (empty($redis->sismember("sorteio","$v"))) {
        $redis->sadd("sorteio","$v");            
        $qtdnum++;
    }
    if ($qtdnum>=15) {
        break;
    }        
}    

$sorteio=implode(",",$redis->smembers("sorteio"));
echo "Pedras sorteadas: $sorteio\n";

// falta desenvolver a seguinte parte:
//Em seguida, verifique cada cartela e pontue no score. O primeiro
//jogador que somar 15 pontos, deve ser colocado como vencedor.

?>
