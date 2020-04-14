# redis

Desenvolvimento da aplicação “Redinsgo” em PHP-CLI e REDIS:

Conexão com o REDIS:
```php
$redis = new Redis(); 
$redis->connect('127.0.0.1', 6379); 

//$redis->flushall();

echo "REDISGO"; 
echo "\n";
echo "Resposta do servidor REDIS ao PING: ".$redis->ping();
echo "\n";
```
Geração das cartelas, utilizando um set com números de 1 a 99 para usar a função SRANDMEMBER:
```php
for ($i=1; $i <= 99; $i++) {
    $redis->sadd("cartela","$i");
}
````

Leitura do usuário e geração dos sets "user:", "cartela:" e "score:":
```php
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
```
Processo de sorteio e rankeamento:
```php
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
```
Listagem dos acertos por usuário e vencedor:
```php
for ($i=1; $i <= $totreg; $i++) {
    echo $redis->hget("user:$i","name")." - Acertos: ".$redis->zscore("score","score:$i")."\n";                
}        

echo "Vencedor: ".$redis->hget($useven,"name")."\n";
```
Aplicação executando no terminal com 5 usuários:
<pre><font color="#8AE234"><b>marco@ubuntu</b></font>:<font color="#729FCF"><b>~/Documents/Aula 2020-04-04</b></font>$ php redinsgo.php 
Aplicativo de bingo - REDISGO
Resposta do servidor REDIS ao PING: +PONG
usuario1 - Cartela: 1,3,16,27,28,38,40,47,50,60,68,75,83,85,94
usuario2 - Cartela: 7,8,20,21,24,33,37,38,39,54,60,61,63,71,82
usuario3 - Cartela: 6,8,17,32,44,46,57,58,65,68,70,72,94,96,97
usuario4 - Cartela: 2,4,9,10,18,25,41,53,55,56,57,79,83,92,96
usuario5 - Cartela: 8,18,20,27,30,37,39,40,41,56,63,69,71,77,93
Pedras sorteadas: 1,2,3,5,6,7,9,10,11,13,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,34,35,37,38,39,40,41,43,44,46,47,48,49,50,51,52,54,55,56,57,58,59,60,61,62,63,64,65,67,68,69,70,71,73,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,92,93,94,95,96,97,98,99
Qtde. pedras sorteadas: 86
usuario1 - Acertos: 15
usuario2 - Acertos: 13
usuario3 - Acertos: 13
usuario4 - Acertos: 13
usuario5 - Acertos: 14
Vencedor: usuario1
</pre>
