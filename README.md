# API-Query

第三方API查询，API厂商地址：api.gpt.ge. 
你也可以根据其他接口地址更改以适应自己的API。（详见 快速使用－配置）

---

## 项目简介

本项目为一个**API额度与用量查询项目**，其功能适配部大分PHP主机环境。  
界面设计采用暗黑荧光风格、高级渐变动画，自适应手机/PC，并内置数字动效、查询时段选择等交互。  
通过填写API令牌（Token）即可实时查询API key的本期用量与额度上限，并支持自定义起止日期查询。  

- 兼容性极高，拷贝即用
- 前端美观、高级、暗黑渐变、动画平滑
- 支持时段筛选
- 快速API配置，适应不同后端API（仅需改config.php）
- 署名与定制方便

---

## 项目结构

```
API-Query/
│
├── config.php        // 配置文件（填写API地址、Token）
└── index.php         // 主前端界面与查询逻辑（核心文件）

```

**文件说明：**

- **config.php**  
    - 项目唯一配置文件，填写：
        - `api_base`：API主域名（如 https://api.gpt.ge）
        - `api_token`：你的API密钥
        - `usage_path`：用量接口
        - `subscription_path`：额度接口
    - 分离配置，方便大规模部署/变更。

- **index.php**  
    - 项目主入口及全部业务逻辑/页面。
    - 通过`stream_context_create`和`file_get_contents`方式发起API请求，PHP主机无需curl扩展。
    - 前端UI全部写于本文件内部，支持选日期查询，金额动效，美观动画，响应式布局。
    - 页脚具名“made by zhuchenyu”。

---

## 快速使用

1. **配置：**  
    填写 config.php 中的 api_token、api_base 及接口路径项。

2. **部署：**  
    上传 config.php 和 index.php 至你的PHP主机/服务器。

3. **访问：**  
    浏览器直接访问 index.php 页面，可视化查询。

4. **定制/扩展：**  
    若需对接其他API或修改额度/用量接口，只需变更config.php相应项即可。


---

**本项目由 zhuchenyu 原创制作，支持二次开发 & 署名应用。**
