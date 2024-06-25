# nombre d'instances
AWS_INSTANCE_NUMBER=2

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
  -auto-approve \
  > tmp/info


# Extraction dns et ip
app_server_public_dns=($(grep -A $AWS_INSTANCE_NUMBER "app_server_public_dns =" tmp/info | tail -n $AWS_INSTANCE_NUMBER | awk -F '"' '{print $2}'))
app_server_public_ip=($(grep -A $AWS_INSTANCE_NUMBER "app_server_public_ip =" tmp/info | tail -n $AWS_INSTANCE_NUMBER | awk -F '"' '{print $2}'))

# Ecriture de l'inventaire Ansible (inventory.ini)
echo "[managers]" > ansible/playbook/inventory.ini
echo "manager ansible_host=${app_server_public_dns[0]} aws_ip=${app_server_public_ip[0]} ansible_user=ubuntu ansible_ssh_private_key_file=myKey.pem" >> ansible/playbook/inventory.ini
echo "[workers]" >> ansible/playbook/inventory.ini
for ((i=1; i<${#app_server_public_dns[@]}; i++)); do
    echo "worker$i ansible_host=${app_server_public_dns[$i]} aws_ip=${app_server_public_ip[$i]} ansible_user=ubuntu ansible_ssh_private_key_file=myKey.pem" >> ansible/playbook/inventory.ini
done

# Construction de l'image Docker d'Ansible
docker build -t ansible_image ./ansible

# Execution du playbook Ansible
docker container run --rm -it ansible_image ansible-playbook -i inventory.ini playbook.yml


