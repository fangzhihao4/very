<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Routing\ResponseFactory;

class ResponseMacroServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @param  ResponseFactory  $factory
     * @return void
     */
    public function boot(ResponseFactory $factory)
    {
        $factory->macro('jsonFormat', function (int $retcode,string $msg,$data='') use ($factory) {
            return $factory->make(json_encode(['retcode'=>$retcode,'msg'=>$msg,'data'=>$data]))->header('Content-type','application/json');
        });
        $factory->macro('success', function (string $message,string $url) use ($factory) {
            $params = ['message'=>$message, 'url'=>$url];
            return redirect('/common/success?'.http_build_query($params));
        });
        $factory->macro('failed', function (string $message,string $url) use ($factory) {
            $params = ['message'=>$message, 'url'=>$url];
            return redirect('/common/failed?'.http_build_query($params));
        });
        $factory->macro('deny', function (string $message,string $url) use ($factory) {
            $params = ['message'=>$message, 'url'=>$url];
            return redirect('/common/deny?'.http_build_query($params));
        });
    }
}