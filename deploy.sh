# nombre d'instances
AWS_INSTANCE_NUMBER=2

echo $AWS_ACCESS_KEY_ID
echo $AWS_SECRET_ACCESS_KEY


docker container run -it --rm \
  --env-file .env \
  -v $PWD/terraform:$PWD \
  -w $PWD \
  -e TF_VAR_aws_instance_number=$AWS_INSTANCE_NUMBER \
  hashicorp/terraform init

docker container run -it --rm \
  --env-file .env \
  -v $PWD/terraform:$PWD \
  -w $PWD \
  -e TF_VAR_aws_instance_number=$AWS_INSTANCE_NUMBER \
  hashicorp/terraform plan

docker container run -it --rm \
  --env-file .env \
  -v $PWD/terraform:$PWD \
  -w $PWD \
  -e TF_VAR_aws_instance_number=$AWS_INSTANCE_NUMBER \
  hashicorp/terraform apply \
  -auto-approve
