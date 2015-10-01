Vagrant.configure(2) do |config|
  config.vm.box = "ubuntu/trusty64"

  config.vm.network "forwarded_port", guest: 80, host: 8081
  config.vm.network "forwarded_port", guest: 3306, host: 33061
  config.vm.synced_folder ".", "/home/vagrant", {:mount_options => ['dmode=755','fmode=644']}
  config.vm.provision :shell, :path => "bootstrap.sh"

  config.hostmanager.enabled = true
  config.hostmanager.manage.host = true
  config.hostmanager.ignore_private_ip = false
  config.vm.provision :hostmanager
  config.vm.define 'url-shortener-box' do |node|
    node.vm.hostname = 'url-shortener-box-hostname'
    node.vm.network :private_network, ip: '192.168.42.42'
    node.hostmanager.aliases = %w(url-shortener.local)
  end

end
