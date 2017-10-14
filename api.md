## Table of contents

- [\Plinker\Tasks\Runner](#class-plinkertasksrunner)
- [\Plinker\Tasks\Model](#class-plinkertasksmodel)
- [\Plinker\Tasks\Lib\PID](#class-plinkertaskslibpid)

<hr />

### Class: \Plinker\Tasks\Runner

| Visibility | Function |
|:-----------|:---------|
| public | <strong>__construct(</strong><em>array</em> <strong>$config=array()</strong>)</strong> : <em>void</em> |
| public | <strong>__get(</strong><em>mixed</em> <strong>$index</strong>)</strong> : <em>void</em><br /><em>Getter</em> |
| public | <strong>__set(</strong><em>mixed</em> <strong>$index</strong>, <em>mixed</em> <strong>$value</strong>)</strong> : <em>void</em><br /><em>Setter</em> |
| public | <strong>daemon(</strong><em>string</em> <strong>$class</strong>, <em>array</em> <strong>$config=array()</strong>)</strong> : <em>void</em><br /><em>Daemon - run continuously for 1 minute.</em> |
| public | <strong>run(</strong><em>string</em> <strong>$class</strong>, <em>array</em> <strong>$config=array()</strong>)</strong> : <em>void</em><br /><em>Run once</em> |

<hr />

### Class: \Plinker\Tasks\Model

| Visibility | Function |
|:-----------|:---------|
| public | <strong>__construct(</strong><em>mixed</em> <strong>$database</strong>)</strong> : <em>void</em> |
| public | <strong>count(</strong><em>mixed</em> <strong>$table</strong>, <em>mixed</em> <strong>$where=null</strong>, <em>array</em> <strong>$params=array()</strong>)</strong> : <em>void</em><br /><em>Count</em> |
| public | <strong>create(</strong><em>array</em> <strong>$data=array()</strong>)</strong> : <em>mixed</em><br /><em>Create</em> |
| public | <strong>exec(</strong><em>mixed</em> <strong>$sql</strong>, <em>mixed</em> <strong>$params=null</strong>)</strong> : <em>void</em><br /><em>Exec</em> |
| public | <strong>export(</strong><em>\RedBeanPHP\OODBBean</em> <strong>$row</strong>)</strong> : <em>void</em><br /><em>Export Exports bean into an array</em> |
| public | <strong>find(</strong><em>mixed</em> <strong>$table=null</strong>, <em>mixed</em> <strong>$where=null</strong>, <em>mixed</em> <strong>$params=null</strong>)</strong> : <em>mixed</em><br /><em>Find</em> |
| public | <strong>findAll(</strong><em>mixed</em> <strong>$table</strong>, <em>mixed</em> <strong>$where=null</strong>, <em>mixed</em> <strong>$params=null</strong>)</strong> : <em>mixed</em><br /><em>Get</em> |
| public | <strong>findOne(</strong><em>mixed</em> <strong>$table=null</strong>, <em>mixed</em> <strong>$where=null</strong>, <em>mixed</em> <strong>$params=null</strong>)</strong> : <em>mixed</em><br /><em>Find One</em> |
| public | <strong>findOrCreate(</strong><em>array</em> <strong>$data=array()</strong>)</strong> : <em>mixed</em><br /><em>findOrCreate</em> |
| public | <strong>load(</strong><em>mixed</em> <strong>$table</strong>, <em>mixed</em> <strong>$id</strong>)</strong> : <em>mixed</em><br /><em>Load (id)</em> |
| public | <strong>nuke()</strong> : <em>void</em><br /><em>Nuke Destroys database</em> |
| public | <strong>store(</strong><em>\RedBeanPHP\OODBBean</em> <strong>$row</strong>)</strong> : <em>void</em><br /><em>Store</em> |
| public | <strong>trash(</strong><em>\RedBeanPHP\OODBBean</em> <strong>$row</strong>)</strong> : <em>void</em><br /><em>Trash Row</em> |
| public | <strong>update(</strong><em>\RedBeanPHP\OODBBean</em> <strong>$row</strong>, <em>array</em> <strong>$data=array()</strong>)</strong> : <em>void</em><br /><em>Update</em> |

<hr />

### Class: \Plinker\Tasks\Lib\PID

| Visibility | Function |
|:-----------|:---------|
| public | <strong>__construct(</strong><em>string</em> <strong>$directory=`''`</strong>, <em>string</em> <strong>$task=`'default'`</strong>)</strong> : <em>void</em> |
| public | <strong>__destruct()</strong> : <em>void</em> |
| public | <strong>script_memory_usage()</strong> : <em>void</em> |

