# How to run Shido Validator guide and optimizations

## Guides

The guides are accesible from this repository as PDF files named as "How to ...".
Their writable sources are also available from this repository under "sources" folder.

## Optimizations

### Auto clean
In order to make your shido node to clean itself (meaning do removed previous and unused transactions and indexes from database), you have to:
Modifiy your `/path_to_your_shidod_folder/config/app.toml` as following:

```
#Prune type
pruning = "custom"

#Prune Strategy
pruning-keep-recent = "100"
pruning-keep-every = "0"
pruning-interval = "10"

```
Modifiy your `/path_to_your_shidod_folder/config/config.toml` as following:
``` indexer = "null" ```

> After all these modifications you have to restart your node, that may that up to 1h to run back properly.
> And your database can take more than 1h to refelect those modifications, depending on your database size.

### Peers

In order to make your shido node more effective regarding blocks synchronizations, you have to ensure that it relies on more than few peers.
So we need to find more seeds other than those provided by shido team.
We can do this easily with the following command on your node (please do not stop your node, open a new shell instead):

```
curl -s http://localhost:26657/net_info | jq -r '.result.peers[] | "\(.node_info.id)@\(.remote_ip):\(.node_info.listen_addr | (split(":")[2]) | select(. != null))" | select(. |  match("([0-9]{1,3}[\\.]){3}[0-9]{1,3}"))'

```

### Seeds

In order to make your shido node more effective regarding blocks synchronizations, you have to switch from "peers" to "seeds" by
modifying your `/path_to_your_shidod_folder/config/config.toml` file as following:

```
#from:

seeds = ""
persistent_peers = "7ed728831ff441d18a8556b64afcaebc31b68c74@3.76.57.158:26656[...]"

#to

seeds = "7ed728831ff441d18a8556b64afcaebc31b68c74@3.76.57.158:26656[...]"
persistent_peers = ""

```
> Here you should update the list with the one gathered from the "Peers" command.
> You have to restart your node to see the effects.
> ** You have an already optimized ``config.toml`` file is this repository at [config.toml](https://github.com/dub1t0/shido-validator/blob/main/config.toml)**



### System tunning
```

apt-get -y install cpufrequtils
sudo su

cat > /etc/default/cpufrequtils << EOF
ENABLE="true"
GOVERNOR="performance"
EOF

systemctl restart cpufrequtils




cat >> /etc/sysctl.conf << EOF
net.core.rmem_max=16777216
net.core.wmem_max=16777216
net.ipv4.tcp_max_syn_backlog=8192
net.core.netdev_max_backlog=65536
net.ipv4.tcp_slow_start_after_idle=0
net.ipv4.tcp_mtu_probing=1
net.ipv4.tcp_sack=0
net.ipv4.tcp_dsack=0
net.ipv4.tcp_fack=0
net.ipv4.tcp_congestion_control=bbr

EOF
sysctl -p


```
> Restart your node





