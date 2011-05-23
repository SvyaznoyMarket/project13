<?php

/**
 * redis:info task
 *
 * @uses      sfBaseTask
 * @package   sfRedisPlugin
 * @author    Benjamin VIELLARD <bicou@bicou.com>
 * @license   The MIT License
 * @version   SVN: $Id: redisInfoTask.class.php 28813 2010-03-26 19:47:10Z bicou $
 */
class redisInfoTask extends sfBaseTask
{
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
    ));

    $this->namespace        = 'redis';
    $this->name             = 'info';
    $this->briefDescription = 'display redis server info';
    $this->detailedDescription = <<<EOF
The [redis:info|INFO] displays redis server info.
Call it with:

  [php symfony redis:info|INFO]
EOF;
  }

  /**
   * execute task
   *
   * @param array $arguments
   * @param array $options
   * @access protected
   * @return void
   */
  protected function execute($arguments = array(), $options = array())
  {
    $redis = sfRedis::getClient($options['connection']);
    $info  = $redis->info();

    $this->logSection('connection                ', $redis->getConnection());
    $this->logSection('version                   ', $info['redis_version']);
    $this->logSection('arch                      ', $info['arch_bits'].' bits');
    $this->logSection('role                      ', $info['role']);
    $this->logSection('multiplexing_api          ', $info['multiplexing_api']);
    $this->logSection('uptime                    ', $info['uptime_in_seconds'].' s');
    $this->logSection('uptime                    ', $info['uptime_in_days'].' d');
    $this->logSection('connected_clients         ', $info['connected_clients']);
    $this->logSection('connected_slaves          ', $info['connected_slaves']);
    $this->logSection('used_memory               ', $info['used_memory']);
    $this->logSection('used_memory_human         ', $info['used_memory_human']);
    $this->logSection('changes_since_last_save   ', $info['changes_since_last_save']);
    $this->logSection('bgsave_in_progress        ', $info['bgsave_in_progress']);
    $this->logSection('last_save_time            ', date(DATE_W3C, $info['last_save_time']));
    $this->logSection('bgrewriteaof_in_progress  ', $info['bgrewriteaof_in_progress']);
    $this->logSection('total_connections_received', $info['total_connections_received']);
    $this->logSection('total_commands_processed  ', $info['total_commands_processed']);

    for ($db = 0; $db < 10; $db++)
    {
        if (isset($info['db'.$db]))
        {
            $this->logSection('db_'.$db.'_keys                 ', $info['db'.$db]['keys']);
            $this->logSection('db_'.$db.'_expires              ', $info['db'.$db]['expires']);
        }
    }
  }
}
