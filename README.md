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





