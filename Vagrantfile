Vagrant.configure(2) do |config|
  config.ssh.username="root"

  config.vm.define "mysql" do |a|
    a.vm.provider "docker" do |d|
      d.name = "mysql"
      d.build_dir = "docker/images/mysql"
      d.vagrant_vagrantfile = "./proxy/Vagrantfile.proxy"
      d.ports = ["3306:3306"]
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
        "mysql:mysql"
      ]
    end
  end
end
