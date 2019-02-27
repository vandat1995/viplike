#### BECOM SUDO USER:
```sh
sudo -s
```

#### INSTALL WEBSTATIC REPO FOR CENTOS/RED HAT 7:
```sh
rpm -Uvh https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm
rpm -Uvh https://mirror.webtatic.com/yum/el7/webtatic-release.rpm
```

#### INSTALL PHP WITH COMMON MODULES:
```sh
yum --nogp install -y --enablerepo=webtatic-testing \
php72w php72w-cli php72w-common php72w-devel \
php72w-gd php72w-intl php72w-mbstring php72w-mcrypt \
php72w-mysqlnd php72w-odbc php72w-opcache php72w-pdo \
php72w-pdo_dblib php72w-pear php72w-pgsql php72w-pspell \
php72w-soap php72w-xml php72w-xmlrpc php72w-bcmath
```

#### CHANGE TO A TEMP DIRECTORY:
```sh
cd /tmp
```

#### PULL DOWN the PTHREADS GIT REPO:
```sh
git clone https://github.com/krakjoe/pthreads.git
cd pthreads
zts-phpize
./configure --with-php-config=/usr/bin/zts-php-config
make
```

#### COPY EXTENSION TO PHP-ZTS MODULES FOLDER:
```sh
cp modules/pthreads.so /usr/lib64/php-zts/modules/.
```

#### ENABLE EXTENSION IN PHP-ZTS, BY CREATING A FILE:
```sh
vi /etc/php-zts.d/pthreads.ini
```

#### ADD THIS TO THE FILE AND SAVE:
```sh
extension=pthreads.so
```

#### NEXT CHECK TO SEE IF YOU GOT IT WORKING:
```sh
zts-php -i | grep -i thread
```

#### IT SHOULD OUPUT SOMETHING LIKE THIS:
```sh
/etc/php-zts.d/pthreads.ini
Thread Safety => enabled
pthreads
```

#### NOW YOU CAN INVOKE PROGRAMS THAT NEED THREADING AND PTHREADS BY USING:
```sh
zts-php (instead of php)
```

Adapted from: https://io.ofbeaton.com/2015/02/pthreads-phpzts-rpms-centos/