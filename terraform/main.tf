terraform {
  required_providers {
    aws = {
      source  = "hashicorp/aws"
      version = "~> 4.16"
    }
  }

  required_version = ">= 1.2.0"
}

provider "aws" {
  region  = "us-east-1"
}

resource "aws_instance" "app_server" {
  count = var.aws_instance_number
  ami           = "ami-04b70fa74e45c3917"
  instance_type = "t2.micro"
  key_name      = "myKey"

  vpc_security_group_ids = [aws_security_group.app_server_sg.id]

  tags = {
    Name = "cloudAwsEfrei"
  }

}

resource "aws_security_group" "app_server_sg" {
  name        = "app_server_sg"
  description = "Allow necessary inbound traffic for app servers"

  ingress {
    from_port   = 22
    to_port     = 22
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
    from_port   = 2377
    to_port     = 2377
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
    from_port   = 7946
    to_port     = 7946
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
    from_port   = 7946
    to_port     = 7946
    protocol    = "udp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
    from_port   = 4789
    to_port     = 4789
    protocol    = "udp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
    from_port   = 80
    to_port     = 80
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }
}

variable "aws_instance_number" {
  type = number
  default = 1
  validation {
    condition = var.aws_instance_number > 1
    error_message = "The number of instances must be greater than 1"
  }
}

output "app_server_public_dns" {
  value = aws_instance.app_server[*].public_dns
}

output "app_server_public_ip" {
  value = aws_instance.app_server[*].public_ip
}