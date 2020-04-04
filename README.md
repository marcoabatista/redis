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
```
Geração das pedras sorteadas:
```php
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
```
Aplicação executando no terminal:
<pre><font color="#8AE234"><b>marco@ubuntu</b></font>:<font color="#729FCF"><b>~/Documents/Aula 2020-04-04</b></font>$ php redinsgo.php
REDISGO
Resposta do servidor REDIS ao PING: +PONG
Usuário: teste1
Cartela: 2,17,23,25,26,30,39,42,43,50,56,70,84,86,93
Usuário: teste2
Cartela: 1,11,14,17,19,20,32,46,50,60,86,87,88,90,96
Pedras sorteadas: 3,12,16,17,24,25,27,28,35,39,59,66,77,80,84
<font color="#8AE234"><b>marco@ubuntu</b></font>:<font color="#729FCF"><b>~/Documents/Aula 2020-04-04</b></font>$ 
</pre>

