FROM nginx

# This is prod image (for dev use just mount your application as host volume into php image we extend here)
ENV SYMFONY_ENV=prod

# Copy in project files into work dir
# COPY . /var/www

# COPY doc/nginx/ez_params.d /etc/nginx/ez_params.d

#CMD /bin/bash -c "cd /var/www && bin/vhost.sh --template-file=doc/nginx/vhost.template > /etc/nginx/conf.d/default.conf && nginx -g 'daemon off;'"
