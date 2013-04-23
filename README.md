Проект "CARGO"
==============

Цель проекта состоит в создании сервиса для...

Установка
---------

Простейшим вариантом для создания окружения является использование виртуальной машины. Чтобы ей воспользоваться
понадобятся:

1. [VirtualBox](https://www.virtualbox.org/wiki/Downloads)
2. [Vagrant](http://downloads.vagrantup.com/)

После установки VirtualBox и Vagrant:

1. Клонируем репозиторий

    ```
    git clone git@github.com:MashaiMedvedi/cargo.git
    ```
2. Переходим в каталог с клонированным проектом

    ```
    cd cargo
    ```
3. Устанавливаем пакеты

    ```
    php composer.phar self-update
    php composer.phar --dev install
    ```
4. Устанавливаем конфигурацию вагранта

    Место установки пакета с конфигурацией вагранта зависит от выбранной схемы. В качестве примера рассмотрим схему с расположением конфигурации вагранта внутри проекта. За описанием других вариантов и более подробной информацией обратитесь к описанию проекта []()

    ```
    git clone git@github.com:MashaiMedvedi/cargo-devenv.git dev
    cd dev
    git submodule update --init --recursive
    cp Vagrantfile.subdir ../Vagrantfile
    cd ..
    ```
4. Запускаем виртуальную машину

    ```
    vagrant up
    ```

5. Настраиваем хост-систему

    Необходимо настроить доступ к сайту в виртуальной машине из хост-системы. Для этого надо добавить маппинг имени в IP и проксирование запросов.
    
    В `/etc/hosts` добавляем

    ```
    127.0.0.1	cargo.dev
    ```

    В nginx добавляем новый сайт
    
    ```
    sudo cp dev/etc/conf/nginx /etc/nginx/site-available/cargo
    sudo ln -s /etc/nginx/site-available/cargo /etc/nginx/site-enabled
    sudo service nginx restart
    ```

На этом установка заканчивается. Можно проверить доступность сайта зайдя браузером по адресу http://cargo.dev
