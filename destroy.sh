# nombre d'instances
AWS_INSTANCE_NUMBER=2

docker container run -it --rm \
  --env-file .env \
  -v $PWD/terraform:$PWD \
  -w $PWD \
  -e TF_VAR_aws_instance_number=$AWS_INSTANCE_NUMBER \
  hashicorp/terraform destroy \
  -auto-approve \
