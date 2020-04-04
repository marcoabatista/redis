# redis

Desenvolvimento da aplicação “Redinsgo” em PHP e REDIS:

Conexão com o REDIS:

$redis = new Redis(); 
$redis->connect('127.0.0.1', 6379); 

//$redis->flushall();

echo "REDISGO"; 
echo "\n";
echo "Resposta do servidor REDIS ao PING: ".$redis->ping();
echo "\n";
