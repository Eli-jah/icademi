FROM nginx:latest

# docker container run --rm --name some-nginx -v /some/content:/usr/share/nginx/html:ro -d nginx
# docker container run --rm --name some-nginx -d some-content-nginx
# docker container run --rm --name some-nginx -d -p 8080:80 some-content-nginx
# docker container run --rm --name my-custom-nginx-container -v /host/path/nginx.conf:/etc/nginx/nginx.conf:ro -d nginx
# docker container run --rm --name tmp-nginx-container -d nginx
# docker cp tmp-nginx-container:/etc/nginx/nginx.conf /host/path/nginx.conf
# docker rm -f tmp-nginx-container
# docker container run --rm --name my-custom-nginx-container -d custom-nginx
# docker container run --rm -d -p 80:80 --read-only -v $(pwd)/nginx-cache:/var/cache/nginx -v $(pwd)/nginx-pid:/var/run nginx
# docker container run --rm --name my-nginx -v /host/path/nginx.conf:/etc/nginx/nginx.conf:ro -d nginx nginx-debug -g 'daemon off;'

# To install basic tools
RUN apt update && apt list --upgradable && apt upgrade -y && apt autoremove -y
RUN apt install -y git wget vim net-tools build-essential

# COPY static-html-directory /usr/share/nginx/html
# COPY nginx.conf /etc/nginx/nginx.conf
COPY ./etc/profile/. /root/
COPY ./etc/nginx/nginx.conf /etc/nginx/nginx.conf
COPY ./etc/nginx/conf.d /etc/nginx/conf.d
# COPY . /usr/share/nginx/html
COPY . /var/www/html
WORKDIR /var/www/html

ENV TZ=Asia/Shanghai
# ENV NGINX_HOST=localhost
# ENV NGINX_PORT=80

EXPOSE 80

# CMD [ "" ]
