if [ ${TOTPSCLASSLIB_DEV_PATH} ] 
then 
    php ${TOTPSCLASSLIB_DEV_PATH}/classlib/refresh.php .
fi
if [ -f ./vendor/symfony/deprecation-contracts/function.php ]
then
    echo -e '\033[0;31m''delete vendor/symfony/deprecation-contracts/function.php for php56'
    rm -f ./vendor/symfony/deprecation-contracts/function.php
    echo -e '\033[1;33m''copy vendor/symfony/deprecation-contracts/function.php for php56'
    cp -f ./202/compatibilityphp/symfony/deprecation-contracts/function.php ./vendor/symfony/deprecation-contracts/function.php
fi
if [ -f ./vendor/symfony/polyfill-php72/Php72.php ]
then
    echo -e '\033[0;31m''delete vendor/symfony/deprecation-contracts/function.php for php56'
    rm -f ./vendor/symfony/polyfill-php72/Php72.php
    echo -e '\033[1;33m''copy vendor/symfony/deprecation-contracts/function.php for php56'
    cp -f ./202/compatibilityphp/symfony/polyfill-php72/Php72.php ./vendor/symfony/polyfill-php72/Php72.php
fi
if [ -f ./vendor/symfony/polyfill-php80/bootstrap.php ]
    echo -e '\033[0;31m''delete vendor/symfony/polyfill-php80/bootstrap.php for php56'
then
    rm -f ./vendor/symfony/polyfill-php80/bootstrap.php
    echo -e '\033[1;33m''copy vendor/symfony/polyfill-php80/bootstrap.php for php56'
    cp -f ./202/compatibilityphp/symfony/polyfill-php80/bootstrap.php ./vendor/symfony/polyfill-php80/bootstrap.php
fi
echo '}' >> ./vendor/symfony/config/Tests/Fixtures/ParseError.php