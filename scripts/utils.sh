# shellcheck shell=bash

# Color variables
utils_red=$(tput setaf 1)
utils_green=$(tput setaf 2)
utils_yellow=$(tput setaf 3)
utils_bold=$(tput bold)
utils_reset=$(tput sgr0)

#
# Remove active docker containers
#
clean_up_containers() {
	# shellcheck disable=SC2207
	alive_container_ids=($(docker ps -q))

	if [[ ${#alive_container_ids[@]} != 0 ]]; then
		echo -n "Clean up active containers ..."
		docker kill "${alive_container_ids[@]}" > /dev/null
		echo " ${utils_green}done${utils_reset}"
	fi

	old_container_ids=($(docker ps -qa))
	if [[ ${#old_container_ids[@]} != 0 ]]; then
		echo -n "Clean up old (inactive) containers ..."
		docker rm "${old_container_ids[@]}" > /dev/null
		echo " ${utils_green}done${utils_reset}"
	fi
}

#
# Wait for docker mysql server to start up
# Parameters: $container $db_name
#
warm_up_db() {
	echo -n "Waiting for mysql "
	while ! docker exec -i "$1" mysql "$2" -u root -e 'show tables' &> /dev/null ; do
		echo -n '.'
		sleep 1
	done
	echo " ${utils_green}done${utils_reset}"
}

#
# Import SQL files to docker mysql server
# Parameters: $container $db_name $files
#
import_db() {
	local container=$1
	local db_name=$2
	shift 2
	local files="$*"

	for sql in $files; do
		echo -n "- $sql ..."
		if docker exec -i "$container" mysql "$db_name" -u root < "$sql"; then
			echo " ${utils_green}✔${utils_reset}"
		else
			echo " ${utils_red}✖${utils_reset}"
		fi
	done
	docker exec -i "$container" touch var/lib/mysql/import_done
}

#
# Truncate database
# Parameters: $container $db_name
#
truncate_db() {
	echo -n "Truncate db ..."
	docker exec -i "$1" mysqladmin -s -f -u root drop "$2" &> /dev/null || true
	docker exec -i "$1" mysqladmin -u root create "$2"
	echo " ${utils_green}done${utils_reset}"
}

#
# Get local ip adress for your machine
# Returns: Your ip-adress (e.g 192.168.1.110)
#
get_local_ip() {
	if command -v ifconfig >/dev/null; then
		ifconfig | grep "inet " | grep -Fv 127.0.0.1 | awk '{print $2}' | sed -n 1p
	else
		ipconfig | grep 192.1 | sed s/.*:// | sed -n 1p | xargs
	fi
}

#
# Print development environment information
#
print_environment_info() {
	echo -e "\n${utils_yellow}You can now view $(basename "$PWD") in the browser.${utils_reset} \n\n"
	echo "${utils_bold}Local:${utils_reset}            http://localhost:${WEB_PORT}/"
	echo "${utils_bold}On Your Network:${utils_reset}  http://$(get_local_ip):${WEB_PORT}/"
	echo -e "\n"
}
