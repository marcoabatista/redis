# redis

Desenvolvimento da aplicação “Redinsgo” em PHP e REDIS:

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
