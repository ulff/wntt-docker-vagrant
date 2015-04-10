Vagrant.configure(2) do |config|
  config.ssh.username="root"

  config.vm.define "nginx" do |a|
    a.vm.provider "docker" do |d|
      d.name = "nginx"
      d.build_dir = "."
      d.vagrant_vagrantfile = "./proxy/Vagrantfile.proxy"
      d.ports = ["80:80"]
      d.volumes = ["/vagrant/:/var/www:rw"]
    end
  end
end
