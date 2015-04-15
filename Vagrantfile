Vagrant.configure(2) do |config|
  config.ssh.username="root"

  config.vm.define "mongo" do |a|
    a.vm.provider "docker" do |d|
      d.name = "mongo"
      d.build_dir = "docker/images/mongo"
      d.vagrant_vagrantfile = "./proxy/Vagrantfile.proxy"
      d.ports = ["27017:27017"]
    end
  end

  config.vm.define "nginx" do |a|
    a.vm.provider "docker" do |d|
      d.name = "nginx"
      d.build_dir = "."
      d.vagrant_vagrantfile = "./proxy/Vagrantfile.proxy"
      d.ports = ["80:80"]
      d.volumes = ["/vagrant/:/var/www:rw"]
      d.create_args = [
        "--link",
        "mongo:mongo"
      ]
    end
  end
end
