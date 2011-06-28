<?php

$folder_id = $folder->getId();

echo _tag("div.control",

  _tag("ul",

    _tag("li", _link("dmMediaLibrary/newFolder?folder_id=".$folder->getId())->text(__("Add a folder here"))->set('.new_folder.dialog_me.s16.s16_folder_add')).

//    _tag("li", _link("dmMediaLibrary/importZip?folder_id=".$folder->getId())->name(__("Import from a zip"))->set('.import_zip.dialog_me')).

    _tag("li", _link("dmMediaLibrary/newFile?folder_id=".$folder->getId())->text(__("Add a file here"))->set('.new_file.dialog_me.s16.s16_file_add')).

    _tag("li.hr", _tag("p", "&nbsp;")).

    (!$folder->isRoot()
    ? _tag("li", _link("dmMediaLibrary/renameFolder?id=".$folder->getId())->text(__("Rename this folder"))->set('.rename_folder.dialog_me.s16.s16_folder_edit'))
    : "").
    (!$folder->isRoot()
    ? _tag("li", _link("dmMediaLibrary/moveFolder?folder_id=".$folder->getId())->text(__("Move this folder"))->set('.move_folder.dialog_me.s16.s16_folder_move'))
    : "").
    (!$folder->isRoot()
    ? _tag("li", _link("dmMediaLibrary/deleteFolder?folder_id=".$folder->getId())->textTitle(__("Delete this folder"))->set('.delete_folder.dm_js_confirm.s16.s16_folder_delete'))
    : "")

  )

);