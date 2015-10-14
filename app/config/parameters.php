<?php 

foreach ($_SERVER as $key => $value) {
    if (strpos($key, 'SYMFONY__') === 0) {
        $paramName = strtolower(substr($key, 9));
        $container->setParameter($paramName, $value);
    }
}