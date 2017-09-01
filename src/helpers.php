<?php

function qd($key = null, $value = null)
{
    $app = app();
    if ($key) {
        if ($value) {
            $app[$key] = $value;
        } else {
            return $app[$key];
        }
    }
    return $app;
}

function basepath($path = "")
{
    return base_path($path);
}

function load_bootstrap()
{
    return include_once basepath("/web/bootstrap.php");
}

function controller($strTableName, $strActionName, $strMethodName = null)
{
    load_bootstrap();
    $folder = QConvertNotation::CamelCaseFromUnderscore($strTableName);
    $token = $strTableName . "_" . $strActionName;
    $filename = "$token.php";
    $class = QConvertNotation::CamelCaseFromUnderscore($token);
    $filepath = __BASEPATH__ . "/app/qd/controllers/" . $folder . "/" . $filename;

    include_once $filepath;

    if ($strMethodName && method_exists($class,$strMethodName)) {
        $instance = new $class;
        $instance->SetInformation($class); // load all information needed suchas User
        $instance->Page_Create(); // default page create
        return $instance->$strMethodName();
    } else {
        ob_start();
        $page = $class::exec();
        $PageContent = ob_get_clean();

        $bc['class'] = !is_empty($page->TaskClassNameBreadCrumb) ? $page->TaskClassNameBreadCrumb : QApplication::Translate(QConvertNotation::WordsFromCamelCase($folder));
        $bc['action'] = !is_empty($page->TaskActionNameBreadCrumb) ? $page->TaskActionNameBreadCrumb : QApplication::Translate($strActionName);
        $page->CustomSubTitle = ($page->CustomSubTitle) ? $page->CustomSubTitle : QApplication::GetBreadcrumb($bc);

        $strScrollNavHtml = $page->getMenu();
        $strTopNavHtml = menu();

        if ($page->GlobalLayout) {
            return include_once(__BASEPATH__ . "/app/qd/views/layout/{$page->GlobalLayout}.php");
        } else {
            return $PageContent;
        }
    }
}

function user($username = "")
{
    load_bootstrap();
    if ($username != "") {
        $objUser = Users::LoadByUsername($username, QQ::Clause(QQ::Expand(QQN::Users()->Role)));
    } else {
        $objUser = @unserialize(QR::getTokenize("objUser"));
        if (!$objUser) {
            $objUser = false;
        }
    }
    return $objUser;
}

function role($blnReturnObject = false)
{
    $objUser = user();

    if ($objUser instanceof Users) {
        $objRole = $objUser->Role;
        $role_name = $objRole->Name;
    } else {
        $objRole = "public";
        $role_name = "public";
    }

    if ($blnReturnObject) {
        return $objRole;
    } else {
        return $role_name;
    }
}

function prepare_routes()
{
    $role_name = strtolower(role());

    $route_filename = basepath("app/qd/routes/{$role_name}_generated.php");

    if (!file_exists($route_filename)) {
        generate_routes();
    }
}

function generate_routes()
{
    load_bootstrap();
    $objRoleArray = Role::LoadAll();
    if ($objRoleArray) {
        $objRoleArray[] = "public";
        foreach ($objRoleArray as $objRole) {
            if ($objRole instanceof Role) {
                $objTaskArray = $objRole->GetTaskArray();
            } else {
                $objTaskArray = Task::QueryArray(QQ::AndCondition(QQ::Equal(QQN::Task()->LinkTypeId, LinkType::_Public)));
            }

            write_routes($objRole, $objTaskArray);
        }
    }
}

function write_routes($objRole, $objTaskArray)
{
    if ($objRole instanceof Role) {
        $role_name = $objRole->Name;
    } else {
        $role_name = $objRole;
    }
    $role_name = strtolower($role_name);

    $folder = basepath('app/qd/routes');
    if(!file_exists($folder)) mkdir($folder);

    $route_filename = $folder . "/{$role_name}_generated.php";
    $route_filename_manual = $folder . "/{$role_name}.php";

    $out = '<?php /* do not edit this route, because it will be override from database */' . "\n";
    if ($objTaskArray) {
        foreach ($objTaskArray as $objTask) {
            $out .= create_route($objTask);
        }
    }

    $fp = fopen($route_filename, "w+");
    fwrite($fp, $out);
    fclose($fp);

    // hanya klo non exist bikin file, karena jangan sampai kena override
    if (!file_exists($route_filename_manual)) {
        $fp = fopen($route_filename_manual, "w+");
        fwrite($fp, '<?php /* custom route for role: ' . $role_name . ' */' . "\n");
        fclose($fp);
    }
}

function create_route(Task $objTask) {
    $url = $objTask->Link;
    $url_params = "/{p1?}/{p2?}/{p3?}/{p4?}/{p5?}/{p6?}/{p7?}";
    $func_params = '$p1=null,$p2=null,$p3=null,$p4=null,$p5=null,$p6=null,$p7=null';
    $controller_params = [
        '"' . $objTask->TableName . '"',
        '"' . $objTask->ActionName . '"',
        '$p1',

    ];
    return 'Route::any("' . $url.$url_params . '", function ('.$func_params.') { return controller(' . implode(",",$controller_params) . '); });' . "\n";
}

function prepare_privileges()
{
    $role_name = strtolower(role());

    $privileges_filename = basepath("app/qd/privileges/{$role_name}.php");

    if (!file_exists($privileges_filename)) {
        generate_privileges();
    }
}

function generate_privileges()
{
    load_bootstrap();
    $objRoleArray = Role::LoadAll();
    if ($objRoleArray) {
        $objRoleArray[] = "public";
        foreach ($objRoleArray as $objRole) {
            if ($objRole instanceof Role) {
                $objTaskArray = $objRole->GetTaskArray();
            } else {
                $objTaskArray = Task::QueryArray(QQ::AndCondition(QQ::Equal(QQN::Task()->LinkTypeId, LinkType::_Public)));
            }

            write_privilege($objRole, $objTaskArray);
        }
    }
}

function write_privilege($objRole, $objTaskArray)
{
    if ($objRole instanceof Role) {
        $role_name = $objRole->Name;
        $role_id = $objRole->Id;
    } else {
        $role_name = $objRole;
        $role_id = 0;
    }
    $role_name = strtolower($role_name);

    $folder = basepath('app/qd/privileges');
    if(!file_exists($folder)) mkdir($folder);

    $privileges_filename = $folder . "/{$role_name}.php";

    $old_privileges = old_privileges($role_id);

    $out = '<?php /* do not edit this privileges, because it will be override from database */' . "\n";
    $privArray = [];
    if ($objTaskArray) {
        foreach ($objTaskArray as $objTask) {
            $data = [
                "independent" => ($objTask->IsIndependent) ? true : false,
                "title" => $objTask->Title,
                "link" => $objTask->Link,
                "table" => $objTask->TableName,
                "table_link" => str_replace("_", "-", $objTask->TableName),
                "class" => $objTask->ClassName,
                "model" => $objTask->ModelName,
                "action" => $objTask->ActionName,
            ];
            $privArray['bytoken'][strtolower($objTask->ModelName . "/" . $objTask->ActionName)] = $objTask->Link;
            $privArray['bymodel'][strtolower($objTask->ModelName)][strtolower($objTask->ActionName)] = $data;
            $privArray["privs"] = $old_privileges["privs"];
            $privArray["privs_global"] = $old_privileges["privs_global"];
            $privArray["task_privs"] = $old_privileges["task_privs"];
            $privArray["system_menus"] = $old_privileges["system_menus"];
            $privArray["link_group_map"] = $old_privileges["link_group_map"];
        }
    }

    $out .= 'return ' . prettyVarExport($privArray) . ";";

    $fp = fopen($privileges_filename, "w+");
    fwrite($fp, $out);
    fclose($fp);
}

function load_routes()
{
    $role_name = strtolower(role());

    $filename_generated = basepath("app/qd/routes/{$role_name}_generated.php");
    $filename_manual = basepath("app/qd/routes/$role_name.php");
    if (file_exists($filename_generated)) {
        require_once $filename_manual;
        require_once $filename_generated;
    } else {
        require_once basepath("app/qd/routes/_default.php");
    }
}

function check_privileges($key)
{

    $role_name = strtolower(role());

    $filename = basepath("app/qd/privileges/$role_name.php");
    if (file_exists($filename)) {
        $privs = require $filename;

        $privs_search = $privs['bytoken'];

        $key = strtolower($key);
        if (array_key_exists($key, $privs_search)) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function get_privileges($bysearch = 'bymodel', $key = null)
{
    $role_name = strtolower(role());

    $filename = basepath("app/qd/privileges/$role_name.php");
    if (file_exists($filename)) {
        $privs = require $filename;

        $privs_search = $privs[$bysearch];

        $key = strtolower($key);
        if ($key && array_key_exists($key, $privs_search)) {
            return $privs_search[$key];
        } else {
            return $privs_search;
        }
    } else {
        return [];
    }
}

function old_privileges($intRoleId = 0)
{
    load_bootstrap();
    if ($intRoleId) {
        $blnLoggedIn = TRUE;
        $cond_menugroup = QQ::AndCondition(
            QQ::OrCondition(QQ::Equal(QQN::MenuGroup()->Role->RoleId, $intRoleId)),
            QQ::OrCondition(QQ::IsNull(QQN::MenuGroup()->ParentMenuId), QQ::Equal(QQN::MenuGroup()->ParentMenuId, 0))
        );
        $cond_task = QQ::OrCondition(QQ::Equal(QQN::Task()->Role->RoleId, $intRoleId));
    } else {
        $blnLoggedIn = FALSE;
        $cond_menugroup = QQ::Equal(QQN::MenuGroup()->IsPublic, TRUE);
        $cond_task = QQ::Equal(QQN::Task()->LinkTypeId, LinkType::_Public);
    }

    $objMenuGroups = MenuGroup::QueryArray($cond_menugroup, QQ::OrderBy(QQN::MenuGroup()->OrderNum));
    $arrMenuGroupAllowed = array();
    if ($objMenuGroups) {
        foreach ($objMenuGroups as $objMenuGroup) {
            $arrMenuGroupAllowed[$objMenuGroup->Name] = $objMenuGroup->Name;
            // visible only when link or child available, but register all menu groups here
            if (!is_empty($objMenuGroup->Link) or $objMenuGroup->CountMenuGroupsAsParentMenu()) {
                if ($blnLoggedIn) {
                    if (in_array(strtolower($objMenuGroup->Link), array("home/login"))) continue;
                }

                $link_group_map[strtolower($objMenuGroup->Link)] = $objMenuGroup->Name;
                $link_group_map[str_replace("_", "", strtolower($objMenuGroup->Link))] = $objMenuGroup->Name;
                $system_menus[strtolower(str_replace(" ", "_", $objMenuGroup->Name))] = array(
                    "id" => "m" . $objMenuGroup->OrderNum,
                    "title" => $objMenuGroup->Name,
                    "link" => $objMenuGroup->Link,
                    "child" => null
                );

                if ($objMenuGroup->CountMenuGroupsAsParentMenu()) {
                    $objChildMenuGroups = MenuGroup::LoadArrayByParentMenuId($objMenuGroup->Id, QQ::Clause(QQ::OrderBy(QQN::MenuGroup()->OrderNum)));
                    foreach ($objChildMenuGroups as $objChildMenuGroup) {
                        $link_group_map[strtolower($objChildMenuGroup->Link)] = $objMenuGroup->Name;
                        $link_group_map[str_replace("_", "", strtolower($objChildMenuGroup->Link))] = $objMenuGroup->Name;
                        $system_menus[strtolower(str_replace(" ", "_", $objMenuGroup->Name))]["child"][strtolower($objChildMenuGroup->Link)] = array(
                            "pid" => "m" . $objMenuGroup->OrderNum,
                            "title" => $objChildMenuGroup->Name,
                            "link" => $objChildMenuGroup->Link,
                            'action' => $objChildMenuGroup->Name
                        );
                    }
                }
            }
        }
    }

    $task_privs = array();
    $privs_global = array();
    $objTasks = Task::QueryArray($cond_task, QQ::Clause(QQ::OrderBy(QQN::Task()->MenuGroup->OrderNum, QQN::Task()->OrderNum, QQN::Task()->Title), QQ::Expand(QQN::Task()->MenuGroup)));
    if ($objTasks) {
        foreach ($objTasks as $objTask) {
            $privs[strtolower($objTask->Link)] = serialize($objTask);
            $privs[str_replace("_", "", strtolower($objTask->Link))] = serialize($objTask);
            $task_privs[strtolower($objTask->ModelName)][strtolower($objTask->ActionName)] = serialize($objTask);
            if ($objTask->IsGlobalMenu) {
                $privs_global[strtolower($objTask->Link)] = serialize($objTask);
            }
            if (array_key_exists(strtolower($objTask->Link), $link_group_map)) {
                // task menu already rendered
            } else {
                $link_group_map[strtolower($objTask->Link)] = $objTask->MenuGroup->Name;
                $link_group_map[str_replace("_", "", strtolower($objTask->Link))] = $objTask->MenuGroup->Name;
                if ($objTask->LinkVisibilityId == LinkVisibilityType::Show) {
                    if (in_array($objTask->MenuGroup->Name, $arrMenuGroupAllowed)) {
                        $system_menus[strtolower(str_replace(" ", "_", $objTask->MenuGroup->Name))]["id"] = "m" . $objTask->MenuGroup->OrderNum;
                        $system_menus[strtolower(str_replace(" ", "_", $objTask->MenuGroup->Name))]["title"] = $objTask->MenuGroup->Name;
                        $system_menus[strtolower(str_replace(" ", "_", $objTask->MenuGroup->Name))]["link"] = "#";
                        $system_menus[strtolower(str_replace(" ", "_", $objTask->MenuGroup->Name))]["child"][strtolower($objTask->Link)] = array(
                            'pid' => "m" . $objTask->MenuGroup->OrderNum,
                            'title' => $objTask->Title,
                            'link' => $objTask->Link,
                            'action' => $objTask->ActionName
                        );
                    }
                }
            }
        }
    }

    return [
        "privs" => $privs,
        "privs_global" => $privs_global,
        "task_privs" => $task_privs,
        "system_menus" => $system_menus,
        "link_group_map" => $link_group_map
    ];
}

function menu()
{
    load_bootstrap();
    $strTopNavHtml = '';
    $strSubNavHtml = '';
    $bc = array();

    $strCurrentRequestUri = QApplication::$PathInfo;
    if (substr($strCurrentRequestUri, 0, 1) == "/") {
        $strCurrentRequestUri = substr($strCurrentRequestUri, 1);
    }
    $arrCurrentRequestUri = explode("/", $strCurrentRequestUri);

    $classfoldername_request = str_replace("-", "_", $strCurrentRequestUri[0]);
    $classfoldername = QConvertNotation::CamelCaseFromUnderscore($classfoldername_request);
    $classtaskname_request = str_replace("-", "_", $strCurrentRequestUri[1]);
    $classtaskname = QConvertNotation::CamelCaseFromUnderscore($classtaskname_request);

    $arrCurrentRequestUriCombination = array();
    for ($i = count($arrCurrentRequestUri) - 1; $i > 0; $i--) {
        $arrCurrentRequestUriCombination[] = strtolower(implode("/", $arrCurrentRequestUri));
        unset($arrCurrentRequestUri[$i]);
    }

    $system_menus = get_privileges("system_menus");
    $link_group_map = get_privileges("link_group_map");
    if (is_array($system_menus)) {
        foreach ($system_menus as $topnav) {
            $currTopNav = '';
            $currTopNav_ = '';
            $currTopNavSelected = '';

            $blnCurrentSelected = false;
            $blnBreadCrumbMenuGroup = false;
            foreach ($arrCurrentRequestUriCombination as $strItemCurrentRequestUri) {
                if (strtolower($topnav['link']) == $strItemCurrentRequestUri) {
                    $blnCurrentSelected = true;
                }

                if (str_replace("_", "", strtolower($topnav['link']) == $strItemCurrentRequestUri)) {
                    $blnCurrentSelected = true;
                }

                if (array_key_exists($strItemCurrentRequestUri, $link_group_map) && strtolower($link_group_map[$strItemCurrentRequestUri]) == strtolower($topnav['title'])) {
                    $blnCurrentSelected = true;
                    $blnBreadCrumbMenuGroup = true;
                }
            }
            if ($blnCurrentSelected) {
                $currTopNav = ' sdb_h_active';
                $currTopNav_ = ' in';
                $currTopNavSelected = ' current';
            }

            if ($blnBreadCrumbMenuGroup) {
                $bc['menugroup'] = $topnav['title'] . "|" . $topnav['id'];
            }

            if ($topnav['link'] == "#") {
                $strTopNavHtmlLink = "#" . $topnav['id'];
            } else {
                $strTopNavHtmlLink = qd_url($topnav['link'], null);
            }
            if (is_array($topnav['child'])) {
                $collapsible = "data-toggle='collapse'";
            } else {
                $collapsible = "";
            }
            $strTopNavHtmlIcon = "<i class='icon-folder-close'></i> ";
            $strTopNavHtmlIcon = "";
            $strTopNavHtml .= "<div class='accordion-group'>\n";
            $strTopNavHtml .= "<div class='accordion-heading{$currTopNav}'>\n";
            $strTopNavHtml .= "<a href='{$strTopNavHtmlLink}' data-parent='#side_accordion' class='accordion-toggle{$currTopNavSelected}' {$collapsible}>\n";
            $strTopNavHtml .= "{$strTopNavHtmlIcon}{$topnav['title']}";
            $strTopNavHtml .= "</a>\n";
            $strTopNavHtml .= "</div>\n";
            if (is_array($topnav['child'])) {
                $strTopNavHtml .= "<div class='accordion-body collapse{$currTopNav_}' id='{$topnav['id']}'>\n";
                $strTopNavHtml .= "<div class='accordion-inner'>\n";
                $strTopNavHtml .= "<ul class='nav nav-list'>\n";
                if (is_array($topnav['child']))
                    foreach ($topnav['child'] as $subnav) {
                        $__temp = explode("/",$subnav['link']);
                        if(count($__temp)>1 and in_array($__temp[0],["activity-log","apis","app-domain","application-registry","incoming-api-log","menu-group","migrations","notifications","outgoing-api-log","role","task","user-token","workflow","workflow-history","workflow-step"])) continue;
                        
                        $strSubnavImploded = preg_replace('/(\/.*)$/', "/list", strtolower(implode("/", array($classfoldername, $classtaskname))));
                        $currSubNav = '';

                        unset($arrCurrentRequestUriCombination[count($arrCurrentRequestUriCombination) - 1]);
                        $arrCurrentRequestUriCombination[] = $strSubnavImploded;
                        $blnCurrentSelected = false;
                        foreach ($arrCurrentRequestUriCombination as $strItemCurrentRequestUri) {
                            if (strtolower($subnav['link']) == $strItemCurrentRequestUri) {
                                $blnCurrentSelected = true;
                            }

                            if (str_replace("_", "", strtolower($subnav['link'])) == $strItemCurrentRequestUri) {
                                $blnCurrentSelected = true;
                            }
                        }
                        if ($blnCurrentSelected) {
                            $currSubNav = 'class="active"';
                        }
                        $strTopNavHtml .= "<li {$currSubNav}>" . href(qd_url($subnav['link'], null), $subnav['title']) . "</li>\n";
                    }
                $strTopNavHtml .= "</ul>\n";
                $strTopNavHtml .= "</div>\n";
                $strTopNavHtml .= "</div>\n";
            }
            $strTopNavHtml .= "</div>\n";
        }
    }

    return $strTopNavHtml;
}

function prettyVarExport($var, array $opts = [])
{
    $opts = array_merge(['indent' => '', 'tab' => '    ', 'array-align' => false], $opts);
    switch (gettype($var)) {
        case 'array':
            $r = [];
            $indexed = array_keys($var) === range(0, count($var) - 1);
            $maxLength = $opts['array-align'] ? max(array_map('strlen', array_map('trim', array_keys($var)))) + 2 : 0;
            foreach ($var as $key => $value) {
                $key = str_replace("'' . \"\\0\" . '*' . \"\\0\" . ", "", prettyVarExport($key));
                $r[] = $opts['indent'] . $opts['tab']
                    . ($indexed ? '' : str_pad($key, $maxLength) . ' => ')
                    . prettyVarExport($value, array_merge($opts, ['indent' => $opts['indent'] . $opts['tab']]));
            }
            return "[\n" . implode(",\n", $r) . "\n" . $opts['indent'] . "]";
        case 'boolean':
            return $var ? 'true' : 'false';
        case 'NULL':
            return 'null';
        default:
            return var_export($var, true);
    }
}

function qdc(){
    return new \QD\Lib\DbComment();
}

function uuid()
{
    return \Ramsey\Uuid\Uuid::uuid4()->toString();
}

function assign_task()
{
    load_bootstrap();
    echo "\n---------[Beginning Processing Task Assignment]---------\n";
    $arrDefault = array(
        "list" => array("link_type" => LinkType::_Protected, "link_visibility" => LinkVisibilityType::Show, "is_independent" => TRUE),
        "new" => array("link_type" => LinkType::_Protected, "link_visibility" => LinkVisibilityType::Hidden, "is_independent" => TRUE),
        "edit" => array("link_type" => LinkType::_Protected, "link_visibility" => LinkVisibilityType::Hidden, "is_independent" => FALSE),
        "view" => array("link_type" => LinkType::_Protected, "link_visibility" => LinkVisibilityType::Hidden, "is_independent" => FALSE),
        "delete" => array("link_type" => LinkType::_Protected, "link_visibility" => LinkVisibilityType::Hidden, "is_independent" => FALSE),
        "export" => array("link_type" => LinkType::_Protected, "link_visibility" => LinkVisibilityType::Hidden, "is_independent" => TRUE),
        "import" => array("link_type" => LinkType::_Protected, "link_visibility" => LinkVisibilityType::Hidden, "is_independent" => TRUE),
        "api" => array("link_type" => LinkType::_Protected, "link_visibility" => LinkVisibilityType::Hidden, "is_independent" => TRUE),
        "listedit" => array("link_type" => LinkType::_Protected, "link_visibility" => LinkVisibilityType::Hidden, "is_independent" => TRUE)
    );

    $arrAdminExcludeClassVisibility = array(
        "activitylog",
        "privatemessage",
        "notifications",
        "task",
        "role",
        "menugroup",
        "privatemessagecontent",
        "setting",
        "usertoken",
        "applicationregistry",
        "apis",
        "incomingapilog",
        "outgoingapilog",
        "appdomain",
        "workflow",
        "workflowstep",
        "workflowhistory",
    );

    $arrAdminExcludeRoleAction = array("import", "export", "listedit");

    $objRoles = Role::LoadAll();
    // browse controller directory
    $controller_dir = __BASEPATH__ . "/app/qd/controllers";
    $d = dir($controller_dir);
    while (false !== ($class_folder_name = $d->read())) {
        if ($class_folder_name != '.' && $class_folder_name != '..' && !preg_match('/^\./', $class_folder_name)) {
            $class_folder_dir = $controller_dir . "/" . $class_folder_name;
            $s = dir($class_folder_dir);
            while (false !== ($class_file_name = $s->read())) {
                if ($class_file_name != '.' && $class_file_name != '..' && !preg_match("/^\./", $class_file_name)) {
                    echo "Processing Task Assignment of $class_file_name\n";
                    $token = substr($class_file_name, 0, strrpos($class_file_name, "."));
                    $task = Task::LoadByToken($token);
                    if (!$task) {
                        $_action_name = substr($token, strlen(QConvertNotation::UnderscoreFromCamelCase($class_folder_name)) + 1);

                        $task = new Task();
                        $task->Token = $token;
                        $task->ClassName = QConvertNotation::CamelCaseFromUnderscore($token);
                        $task->ModelName = $class_folder_name;
                        $task->TableName = QConvertNotation::UnderscoreFromCamelCase($class_folder_name);
                        $task->ActionName = $_action_name;
                        $task->Filename = $class_file_name;
                        if ($task->ActionName == 'list' || $task->ActionName == 'listedit') {
                            $title_prefix = "";
                        } else {
                            $title_prefix = ucwords(str_replace("_", " ", $task->ActionName));
                        }
                        if ($task->TableName == "home") {
                            $title_suffix = "";
                            $blnHome = true;
                        } else {
                            $title_suffix = ucwords(str_replace("_", " ", $task->TableName));
                            $blnHome = false;
                        }
                        $title = trim(QApplication::Translate($title_prefix) . ' ' . QApplication::Translate($title_suffix));
                        $task->Title = $title;
                        $task->Link = strtolower(str_replace("_","-",$task->TableName)) . "/" . $task->ActionName;
                        if ($blnHome) {
                            if (in_array($task->ActionName, array("index", "login", "logout"))) {
                                $task->LinkTypeId = LinkType::_Public;
                            }
                        } else {
                            $task->LinkTypeId = (array_key_exists($task->ActionName, $arrDefault) && $arrDefault[$task->ActionName]['link_type']) ? $arrDefault[$task->ActionName]['link_type'] : LinkType::_Protected;
                        }
                        if (in_array(strtolower($task->ClassName), $arrAdminExcludeClassVisibility)) {
                            $task->LinkVisibilityId = LinkVisibilityType::Hidden;
                        } else {
                            $task->LinkVisibilityId = (array_key_exists($task->ActionName, $arrDefault) && $arrDefault[$task->ActionName]['link_visibility']) ? $arrDefault[$task->ActionName]['link_visibility'] : LinkVisibilityType::Hidden;
                        }
                        $task->MenuGroupId = 1;
                        $task->OrderNum = 0;
                        $task->IsGlobalMenu = 0;
                        if ($blnHome) {
                            $task->IsIndependent = 1;
                        } else {
                            $task->IsIndependent = (array_key_exists($task->ActionName, $arrDefault) && $arrDefault[$task->ActionName]['is_independent']) ? $arrDefault[$task->ActionName]['is_independent'] : 0;
                        }


                        $task->Save(); //group root belum kepake

                        foreach ($objRoles as $objRole) {
                            if ($blnHome) {
                                if($task->ActionName <> "login") {
                                    $task->AssociateRole($objRole);
                                }
                            } else {
                                if ($objRole->Id == 1) {
                                    if (in_array(strtolower($task->ActionName), $arrAdminExcludeRoleAction)) {
                                        // skip
                                    } else {
                                        $task->AssociateRole($objRole);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $s->close();
        }
    }
    $d->close();
}