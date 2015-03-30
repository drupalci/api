# Drupal CI API.
#
# Provides a local development environment for Drupal's Continuous Integration
# Jenkins based platform.
#

box      = 'ubuntu/trusty64'
hostname = 'drupalci-api'
domain   = 'dev'
cpus     = '1'
ram      = '768'

Vagrant.configure("2") do |config|
  config.vm.box      = box
  config.vm.hostname = hostname + '.' + domain

  # Network configured as per bit.ly/1e0ZU1r
  if Vagrant.has_plugin?('vagrant-auto_network')
    # Network configured as per bit.ly/1e0ZU1r
    config.vm.network :private_network, :ip => "0.0.0.0", :auto_network => true
  else
    config.vm.network :private_network, :ip => "192.168.50.10"
  end

  # We want to cater for both Unix and Windows.
  if RUBY_PLATFORM =~ /linux|darwin/
    config.vm.synced_folder(
      ".",
      "/var/www/api",
      :nfs => true,
      :map_uid => 0,
      :map_gid => 0,
     )
  else
    config.vm.synced_folder ".", "/var/www/api"
  end

  # Virtualbox provider configuration.
  config.vm.provider :virtualbox do |vb|
    vb.customize ["modifyvm",     :id, "--cpus", cpus]
    vb.customize ["modifyvm",     :id, "--memory", ram]
    vb.customize ["modifyvm",     :id, "--natdnshostresolver1", "on"]
    vb.customize ["modifyvm",     :id, "--natdnsproxy1", "on"]
    vb.customize ["modifyvm",     :id, "--nicpromisc1", "allow-all"]
    vb.customize ["modifyvm",     :id, "--nicpromisc2", "allow-all"]
    vb.customize ["modifyvm",     :id, "--nictype1", "Am79C973"]
    vb.customize ["modifyvm",     :id, "--nictype2", "Am79C973"]
    vb.customize ["setextradata", :id, "VBoxInternal2/SharedFoldersEnableSymlinksCreate/v-root", "1"]
  end

  # Provision.
  config.vm.provision :shell, :path => "puppet/scripts/vagrant.sh"
end
