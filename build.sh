#!/bin/sh

# 项目发布工具
# author: lenbo
# date: 20170802

# 项目名称
projectName="kuaidai"

# Git地址
gitRepo="git@github.com:lbogcn/kuaidai.git"

# 发布目录
releasePath="/home/wwwroot"

basePath=$(cd `dirname $0`; pwd)
fileName=${projectName}_`date +"%Y%m%d%H%M%S"`
baseDeployPath=${basePath}/deploy/${projectName}
deployPath=${baseDeployPath}/${fileName}

# 创建项目目录
if [ ! -d "${baseDeployPath}" ]; then
  echo "创建项目目录"
  mkdir -p "${baseDeployPath}"
fi

# 创建代码仓库
if [ ! -d "${baseDeployPath}/repo" ]; then
  echo "创建代码仓库"
  git clone ${gitRepo} "${baseDeployPath}/repo"
fi

# 检出最新master并打包
echo "检出最新master并打包"
cd "${baseDeployPath}/repo"
git checkout . && git clean -xdf && git pull && git checkout master
git archive --format zip --output "${deployPath}.zip" master -0

# 进入部署目录并解包
echo "进入部署目录并解包"
cd "${baseDeployPath}"
unzip -d ${fileName} "${deployPath}.zip" > /dev/null
rm -f "${deployPath}.zip"

# 创建软链
echo "创建软链"
cd "${baseDeployPath}"
rm -f "${releasePath}/${projectName}"
ln -s "${deployPath}" "${releasePath}/${projectName}"

# 清理，保留最新5个版本
echo "清理，保留最新5个版本"
cd ${baseDeployPath}
ls -t | grep "${projectName}" | awk 'NR>5 {system("rm -rf \"" $0 "\"")}'

# 进入项目目录，执行后置操作
echo "进入项目目录，执行后置操作"
cd ${deployPath}

chmod -R 777 ./storage
chmod -R 777 ./bootstrap/cache

/usr/local/php/bin/php artisan optimize --force
/usr/local/php/bin/php artisan config:cache
/usr/local/php/bin/php artisan route:cache

chown -R www:www ${deployPath}

/etc/init.d/php-fpm restart
