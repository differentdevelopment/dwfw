<?php

return [
    'allow_list' => explode(',', env('ALLOW_LIST', '*')),
    'block_list' => explode(',', env('BLOCK_LIST', '')),
];
