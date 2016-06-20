<?php 

foreach ($_SERVER as $key => $value) {
    if ($key === 'SYMFONY__ELASTICSEARCH_HOSTS') {
        $config = $container->getParameter('elasticsearch');
        $config['hosts'] = [$value];
        $container->setParameter('elasticsearch', $config);
    } elseif (strpos($key, 'SYMFONY__') === 0) {
        $paramName = strtolower(substr($key, 9));
        $container->setParameter($paramName, $value);
    }
}