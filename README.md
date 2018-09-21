# alfred-workflow-qshell

> qshell 是基于七牛 API 参考手册实现的一个方便开发者测试和使用七牛 API 服务的命令行工具。

**alfred-workflow-qshell** 是一个配合七牛开发者工具 [qshell][qshell-doc] 使用的 Alfred workflow。它使用了可视化流程后台执行 `qshell` 命令行，使得文件上传、查询等功能更易用和高效。


## 功能 & 特点
#### 功能
- 支持多文件上传
- 支持上传文件添加前缀
- 获取空间文件外链
- 查看空间文件基本信息
- 移动、复制、删除、重命名
- 预览、下载空间文件
- 完善的文件操作反馈（通知、错误等）

#### 特点
- curl 下载文件
- 不支持文件夹上传
- 要求空间的文件名不能为空或全为空格

## 使用
#### 上传文件

- 默认关键字 `fput` 搜索本地单个文件，上传文件
- 搜索文件时使用 **Buffer** 功能进行文件多选，上传多文件（[Buffer 的使用]）
- 选中一或多个文件，调出 **Actions** 文件操作菜单，上传文件

#### 操作文件
- 默认关键字 `fstat` 搜索空间文件
- 移动、复制、删除、重命名、外链、信息、预览、下载


## 安装 & 配置
要求：**qshell**、**Alfred with Powerpack**

### 1. [qshell][qshell-doc]
或前往 [GitHub][qshell-github]

安装：只需要下载 zip 包之后解压即可使用。Mac 64 位系统只需要解压后的文件 `qshell-darwin-x64`。把此文件重命名为 `qshell` 后放到 `/usr/local/bin` 目录。

配置：从七牛的后台的账号设置中获取 **AccessKey** 和 **SecretKey**。使用终端执行以下命令，配置本地 qshell：

```
# 设置密钥
qshell account [AccessKey] [SecretKey]
# 查看设置
qshell account
```
注意：上面的设置命令不需要输入方括号 `[]`。

配置 qshell 完毕后会生成 `~/.qshell` 文件夹，其中 `account.json` 文件保存了 AccessKey 和 SecretKey 信息。


### 2. [Alfred][alfred]
下载 [安装包][qshell-dl] 安装即可。

注意：Alfred 需要购买 [Powerpack][alfred-pp] 才能解锁 workflows 功能。


## Todo
- [x] account，显示当前用户的 AccessKey 和 SecretKey
- [x] fput，以文件表单的方式上传一个文件
- [x] stat，查询七牛空间中一个文件的基本信息
- [x] delete，删除七牛空间中的一个文件
- [x] move，移动或重命名七牛空间中的一个文件
- [x] copy，复制七牛空间中的一个文件
- [x] 查新基本信息时可以获取文件外链
- [x] 预览功能
- [x] 下载功能


## 关于
### LICENSE
[MIT License](./LICENSE)


[qshell-doc]: http://developer.qiniu.com/code/v6/tool/qshell.html
[qshell-github]: https://github.com/qiniu/qshell

[alfred]: https://www.alfredapp.com/
[alfred-pp]: https://www.alfredapp.com/powerpack/buy/

[Buffer 的使用]: https://github.com/onestark/better-series/blob/master/better-workflow.md

[qshell-dl]: https://github.com/onestark/alfred-workflow-qshell/raw/master/downloads/qshell.alfredworkflow
