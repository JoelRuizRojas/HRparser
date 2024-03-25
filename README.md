# HRparser

This is a PHP-based web application for user authentication, including sign-in, sign-up, and password recovery functionalities. It can be deployed using a Kubernetes cluster with the provided manifest files.

The development of this site can be made using docker compose to quickly test the deployment of the site. A kaniko-builder tool is added to build the target images to be used by the Kubernetes cluster in a production scenario.

## Features

- User Sign-In: Allows existing users to sign in using their credentials.
- User Sign-Up: Enables new users to create an account with a unique username and password.
- Password Recovery: Provides a password recovery mechanism for users who forget their passwords.

## Technologies Used

- PHP: The backend logic and server-side scripting language used for user authentication.
- HTML/CSS: Frontend markup and styling for the user interface.
- Javascript: Frontend logic to handle the user interface dialogs.
- MySQL: Database management system for storing user credentials securely.
- Kubernetes: Container orchestration platform used to deploy and manage the application in a scalable and reliable manner.
- MinIO Cluster: Distributed object storage system used to store website resources such as images, files, and media content.

## Deployment Instructions

It is required the deployment of two Kubernetes clusters, one cluster is used to deploy the site and another one is required to deploy the MinIO cluster.

### MinIO cluster deployment

With a cluster running, to deploy the MinIO operator, follow the instructions at https://min.io/docs/minio/kubernetes/upstream/operations/installation.html.

To create the MinIO tenants you can use the MinIO operator console user interface to deploy the MinIO servers based on your needs.

Once the MinIO cluster is up and running, the site requires the creation of 2 buckets (`myminio` is the alias for the MinIO loadbalancer server):

```bash
mc mb myminio/myresources
```

```bash
mc mb myminio/myprivateresources
```

The first bucket `myresources` is a public bucket intended to store one of the website images that will be requested from client side once the website is served (so the bucket has to be configured as public). The second bucket `myprivateresources` is intended to store another image that will be requested from backend side and for which the Amazon S3 php utilities are used to perform the S3 request to the MinIO cluster.

To configure the bucket `myresources` as a public bucket:

```bash
mc anonymous set public myminio/myresources
```

Copy the next resouces to the created buckets:

```bash
mc cp <repositoryPath>/public/img/HRparser_LoginImg.jpg myminio/myresources
```

```bash
mc cp <repositoryPath>/public/img/mail.png myminio/myprivateresources
```

### HRparser site deployment

Once the second cluster is up and running to deploy the site, next secret and configmap are needed to set up the configurations to hit the MinIO server:

```bash
kubectl create secret generic minio-secret --from-literal=MINIO_ROOT_USER=<username> --from-literal=MINIO_ROOT_PASSWORD=<password> --namespace=hrparser
```

Credentials used by the login site to perform the S3 request to MinIO server.

```bash
kubectl create configmap minio-configmap --from-literal=MINIO_IP=<ipAddress> --from-literal=MINIO_PORT=<port> --from-literal=MY_RESOURCES_BUCKET_NAME=myresources --from-literal=MY_PRIV_RESOURCES_BUCKET_NAME=myprivateresources --namespace=hrparser
```

As of now, there is no load balancer server defined for the MinIO cluster servers. So, the site intends to directly connect to the nodePort interface for the MinIO tenants, using the MinIO cluster control plane node address and nodePort.

This site, uses a separate pod to run the databases for customer(s) authentication:

```bash
kubectl create secret generic mysql-secret --from-literal=MYSQL_ROOT_USER=root --from-literal=MYSQL_ROOT_PASSWORD=<password> --namespace=hrparser
```

```bash
kubectl create configmap mysql-configmap --from-literal=MYSQL_DATABASE=HRparserDB --from-literal=MYSQL_HOST=localhost --from-literal=MYSQL_PORT=3306 --namespace=hrparser
```

Once all previous resources are set up, define the site volumes and execute the site services and pods with:

```bash
kubectl apply -f login-volumes.yaml
```

```bash
kubectl apply -f login-services.yaml
```

```bash
kubectl apply -f login-pods.yaml
```

Note: For development, the site can be deployed using docker compose.
