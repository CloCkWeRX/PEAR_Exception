--TEST--
channel-update command (remote channel name, changes channel name)
--SKIPIF--
<?php
if (!getenv('PHP_PEAR_RUNTESTS')) {
    echo 'skip';
}
?>
--FILE--
<?php
error_reporting(E_ALL);
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'setup.php.inc';
$reg = &$config->getRegistry();
$c = &$reg->getChannel(strtolower('pear.php.net'));
$lastmod = $c->lastModified();
$GLOBALS['pearweb']->addXmlrpcConfig('pear.php.net', 'channel.update',
    array($lastmod),
    '<?xml version="1.0" encoding="ISO-8859-1"?>
<channel version="1.0" xmlns="http://pear.php.net/channel-1.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://pear.php.net/channel-1.0
http://pear.php.net/dtd/channel-1.0.xsd">
 <name>oops.we.changedit</name>
 <suggestedalias>pear</suggestedalias>
 <summary>foo</summary>
 <servers>
  <primary host="pear.php.net">
   <xmlrpc>
    <function version="1.0">logintest</function>
    <function version="1.0">package.listLatestReleases</function>
    <function version="1.0">package.listAll</function>
    <function version="1.0">package.info</function>
    <function version="1.0">package.getDownloadURL</function>
    <function version="1.0">channel.update</function>
    <function version="1.0">channel.listAll</function>
   </xmlrpc>
  </primary>
 </servers>
</channel>');
$GLOBALS['pearweb']->addXmlrpcConfig('pear.php.net', 'channel.update',
    array(1),
    '<?xml version="1.0" encoding="ISO-8859-1"?>
<channel version="1.0" xmlns="http://pear.php.net/channel-1.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://pear.php.net/channel-1.0
http://pear.php.net/dtd/channel-1.0.xsd">
 <name>oops.we.changedit</name>
 <suggestedalias>pear</suggestedalias>
 <summary>foo</summary>
 <servers>
  <primary host="pear.php.net">
   <xmlrpc>
    <function version="1.0">logintest</function>
    <function version="1.0">package.listLatestReleases</function>
    <function version="1.0">package.listAll</function>
    <function version="1.0">package.info</function>
    <function version="1.0">package.getDownloadURL</function>
    <function version="1.0">channel.update</function>
    <function version="1.0">channel.listAll</function>
   </xmlrpc>
  </primary>
 </servers>
</channel>');
$e = $command->run('channel-update', array(), array('pear.php.net'));
$phpunit->assertErrors(array(
    array('package' => 'PEAR_Error', 'message' =>
        'ERROR: downloaded channel definition file for channel "oops.we.changedit" from channel "pear.php.net"'),
), 'after');
$phpunit->assertEquals(array (
  0 =>
  array (
    'info' => 'Retrieving channel.xml from remote server',
    'cmd' => 'no command',
  ),
), $fakelog->getLog(), 'log');

$reg = &new PEAR_Registry($temp_path . DIRECTORY_SEPARATOR . 'php');
$chan = $reg->getChannel('pear.php.net');
$phpunit->assertIsA('PEAR_ChannelFile', $chan, 'updated ok?');
$phpunit->assertEquals('pear.php.net', $chan->getName(), 'name ok?');
$phpunit->assertEquals('PHP Extension and Application Repository', $chan->getSummary(), 'summary ok?');


$e = $command->run('channel-update', array('force' => true), array('pear.php.net'));
$phpunit->assertErrors(array(
    array('package' => 'PEAR_Error', 'message' =>
        'Error: Channel "oops.we.changedit" does not exist, use channel-add to add an entry'),
), 'after force');
$phpunit->assertEquals(array (
  0 =>
  array (
    'info' => 'Retrieving channel.xml from remote server',
    'cmd' => 'no command',
  ),
  1 =>
  array (
    0 => 0,
    1 => 'WARNING: downloaded channel definition file for channel "oops.we.changedit" from channel "pear.php.net"',
  ),
), $fakelog->getLog(), 'log force');

$reg = &new PEAR_Registry($temp_path . DIRECTORY_SEPARATOR . 'php');
$chan = $reg->getChannel('pear.php.net');
$phpunit->assertIsA('PEAR_ChannelFile', $chan, 'updated ok?');
$phpunit->assertEquals('pear.php.net', $chan->getName(), 'name ok?');
$phpunit->assertEquals('PHP Extension and Application Repository', $chan->getSummary(), 'summary ok?');
echo 'tests done';
?>
--EXPECT--
tests done
