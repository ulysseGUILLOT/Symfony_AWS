# applique les variables d'env (clées AWS)
export $(cat .env | xargs)

docker container run -it --rm \
  -v $PWD/terraform:$PWD \
  -w $PWD \
  -e AWS_ACCESS_KEY_ID=$AWS_ACCESS_KEY_ID \
  -e AWS_SECRET_ACCESS_KEY=$AWS_SECRET_ACCESS_KEY \
  hashicorp/terraform init
#docker container run -it -v $PWD:$PWD -w $PWD hashicorp/terraform plan
#docker container run -it -v $PWD:$PWD -w $PWD hashicorp/terraform apply
#docker container run -it -v $PWD:$PWD -w $PWD hashicorp/terraform destroy
