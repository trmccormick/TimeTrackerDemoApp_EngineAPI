<?php
    $localvars = localvars::getInstance();

    $localvars->set('siteRoot','/');
    $localvars->set('dbConnectionName', 'appDB');
    $localvars->set("appTitle","MediaArchive");
    $localvars->set("meta_authors", "Tracy A. McCormick");
    $localvars->set('appName', "MediaArchive 5000");

    $root = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
    $localvars->set('root', $root);

?>
