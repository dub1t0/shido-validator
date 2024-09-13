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

### Peers / Seeds

In order to make your shido node more effective regarding blocks synchronizations, you have to ensure that it relies on more than few peers / seeds.
So we need to find more seeds other than those provided by shido team.
We can do this easily with the following command on your node (please do not stop your node, open a new shell instead):

```
curl -s http://localhost:26657/net_info | jq -r '.result.peers[] | "\(.node_info.id)@\(.remote_ip):\(.node_info.listen_addr | (split(":")[2]) | select(. != null))" | select(. |  match("([0-9]{1,3}[\\.]){3}[0-9]{1,3}"))'

```





