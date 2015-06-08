DevShop Rancher
===============

This is an experimental project to connect devshop to Rancher to enable containerized environments.

See http://rancher.com for more information.

TODO
----

DevShop server can connect to the Rancher server API.

Next step is to use the `Provision_Service_db_rancher` class to trigger the 
creation of a Rancher environment, then create the services associated with
that environment.

docker-compose
--------------

I do not yet know if Rancher supports "importing" an environment from a 
`docker-compose.yml` file.  

This would be ideal.  Once it does, we can create an "Environment template" for
devshop environments and use `Provision_Config_Rancher_Site` class to manage
creating and sending a docker-compose.yml file to the Rancher agents.

Until then, we will use the API to manually create and enable the Rancher services.