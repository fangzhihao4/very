##nginx配置
```
server {
    listen       80;
    server_name  laravel.admin.verystar.com;
    root         /open/public;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        include fastcgi.conf;
    }
}
```