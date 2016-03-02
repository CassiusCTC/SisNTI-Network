<?php

use  Symfony \Component\HttpKernel\Config\FileLocator;
class   PathClass
{
    private  $fileLocator;
    public function  __construct( FileLocator  $fileLocator)
    {
        $thi->fileLocator = $fileLocator;
    }
    public   function  myMethod()
    {
        $path = $this­->fileLocator­->locate('@SytemSGBundle/Resources/file_routers/file_routers.txt')
    }
}
