load:
    image: tutum/haproxy
    environment:
        VIRTUAL_HOST: <?php print $virtual_hosts . "\n"; ?>

    links:
        - app
    expose:
        - 80/tcp
    ports:
        - '80'
    restart: on-failure
app:
    image: terra/drupal
    tty: true
    stdin_open: true
    links:
        - database
    volumes:
        - '<?php print $environment->repo_root; ?>:/app'
    environment:
        HOST_UID: <?php print $host_uid . "\n" ?>
        HOST_GID: <?php print $host_gid . "\n" ?>
        DOCUMENT_ROOT: <?php print $document_root . "\n" ?>
    expose:
        - 80/tcp
    restart: on-failure
database:
    image: mariadb
    tty: true
    stdin_open: true
    environment:
        MYSQL_ROOT_PASSWORD: <?php print $mysql_root_password . "\n" ?>
        MYSQL_DATABASE: <?php print urlencode($db_name) . "\n"; ?>
        MYSQL_USER: <?php print urlencode($db_user) . "\n"; ?>
        MYSQL_PASSWORD:  <?php print urlencode($db_passwd) . "\n"; ?>
    restart: on-failure
drush:
    image: terra/drush
    tty: true
    stdin_open: true
    links:
        - database
    ports:
        - '22'
    volumes:
        - '<?php print $environment->root; ?>:/var/www/html'
        - '<?php print $environment->repo_root; ?>:/source'
    environment:
        AUTHORIZED_KEYS: "<?php print $authorized_keys ?>"
    restart: on-failure
