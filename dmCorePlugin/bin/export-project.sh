#!/bin/bash

##########################################################################
# dmProjectExporter
#
# copyright   	(C) 2009 - Barberis Marco (moreweb) 
# link					http://www.moreweb.it
# license				http://www.moreweb.it/license
#
# version     	$Rev$
# author      	$Author$
# lastmodified	$Date$
##########################################################################

# Command
RSYNC="$(which rsync) --exclude-from=${HOME}/.rsync/exclude -aC"
MYSQLDUMP="$(which mysqldump)"
TAR="$(which tar) czPf"
GZIP="$(which gzip) -9"
SED="$(which sed)"
GREP="$(which grep)"
MOVE="$(which mv)"
REMOVE="$(which rm) -rf"
MKDIR="$(which mkdir) -p"
CUT="$(which cut)"
TR="$(which tr)"

## Functions
function mmsg () {
  echo -ne "$1\n"
}

function ask_to_continue() {
  read -p "Continue? [Y/n] " answer
  if [ "${answer}" != "" ] && [ "${answer}" != "Y" ] && [ "${answer}" != "y" ]; then
    mmsg "Aborted.";
    exit 0;
  fi
}

function doSqlArchive () {
	# found first dsn connection and clear string
	conn=`${GREP} -m1 "dsn:" ${db_file} | ${CUT} -c 12- |  ${TR} -d "'"`
		
	if [[ $conn =~ (mysql):\/\/([a-zA-Z0-9._-]*):([a-zA-Z0-9._-]*)@([a-z0-9._-]*)/([a-zA-Z0-9._-]*) ]] ;
		then
			dbType=${BASH_REMATCH[1]};
	 		dbUser=${BASH_REMATCH[2]};
	 		dbPass=${BASH_REMATCH[3]};
	 		dbHost=${BASH_REMATCH[4]};
	 		dbName=${BASH_REMATCH[5]};

			${MYSQLDUMP} -u ${dbUser} -h ${dbHost} -p${dbPass} ${dbName} | ${GZIP} > ${db_gz_file}
			mmsg "--> ${db_name} archive created"
						
	else
			mmsg "--> No dsn string founded. (!!!)"
			mmsg
			exit 0;
	fi	
}
 
function clearDbDsn () {	
	${SED} 's/\(mysql:\/\/\)\(.*\)\(@\)/\1%username%:%password%\3/g' ${db_file} > "${db_file}.clear"
	${MOVE} "${db_file}.clear"  "${db_file}"
	mmsg "--> database keys cleread"
}

function clearRecaptchaKeys () {
	filetoclear="${exp_folder}/${prj_name}/apps/front/config/app.yml"
	if [ -f  "${filetoclear}" ]
	  then
			${SED} 's/\(public_key:\)\( *\)\(.*\)/\1\2%recaptcha_public_key%/g;s/\(private_key:\)\( *\)\(.*\)/\1\2%recaptcha_private_key%/g' ${filetoclear} > "${filetoclear}.clear"
			${MOVE} "${filetoclear}.clear"  "${filetoclear}"
			mmsg "--> recaptcha keys cleread"
	else
		mmsg "--> ${filetoclear} does not exist. (!!!)"
		mmsg
		exit 0;
	fi
}

# If no input parameter is given, echo usage and exit
if [ $# -eq 0 ]
  then
    mmsg "Usage: dmProjectExporter {PROJECT_NAME}"
    exit
fi

# Setting up some vars
datestamp=`date +'%Y%m%d'`;

prj_name=$1;
prj_folder="${WEB_ROOT}/${prj_name}";
exp_folder="${EXP_ROOT}/${datestamp}-${prj_name}";

db_file="${exp_folder}/${prj_name}/config/databases.yml";
db_gz_file="${exp_folder}/${prj_name}/data/mysql/${prj_name}.sql.gz";

## Start export operations
clear

mmsg
mmsg "dmProjectExporter"
mmsg "------------------------------------------------------------------------"
mmsg "project name: ${prj_name}"
mmsg "project folder: ${prj_folder}"
mmsg
mmsg "project_export_path: ${exp_folder}"
mmsg "------------------------------------------------------------------------"
mmsg

mmsg "Start to export project." 
ask_to_continue;

mmsg

mmsg "-> Create ${exp_folder} folder.."
${MKDIR} ${exp_folder};
mmsg "..done."

mmsg

mmsg "-> Copy project ${prj_name} to ${exp_folder}.."
${RSYNC} ${prj_folder} ${exp_folder};
mmsg "..done."

mmsg

mmsg "-> Clear unwanted data (cache/* and log/*).."
${REMOVE} ${exp_folder}/${prj_name}/{cache,log}/*;
mmsg "..done."

mmsg

mmsg "-> Create database archive.."
doSqlArchive;
mmsg "..done."

mmsg

mmsg "-> Protect private data.."
clearDbDsn;
clearRecaptchaKeys;
mmsg "..done."

mmsg

mmsg "-> Make ${prj_name} archive (${prj_name}.tar.gz).."
pushd ${exp_folder} > /dev/null
${TAR} ${prj_name}.tar.gz ${prj_name}
popd > /dev/null
mmsg "..done."

mmsg

mmsg "Finished!! :)"

mmsg

exit;