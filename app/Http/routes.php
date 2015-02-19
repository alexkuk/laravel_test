<?php

foreach (File::allFiles(__DIR__ . '/routes') as $partialRoute) {
    require_once $partialRoute->getPathname();
}