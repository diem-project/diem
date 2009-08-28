<?php

$folder_id = $folder->getId();

echo £("div.control",

  £("ul",

    £("li", £link("dmMediaLibrary/newFolder?folder_id=".$folder->getId())->text(__("Add a folder here"))->set('.new_folder.dialog_me.s16.s16_folder_add')).

//    £("li", £link("dmMediaLibrary/importZip?folder_id=".$folder->getId())->name(__("Import from a zip"))->set('.import_zip.dialog_me')).

    £("li", £link("dmMediaLibrary/newFile?folder_id=".$folder->getId())->text(__("Add a file here"))->set('.new_file.dialog_me.s16.s16_file_add')).

    £("li.hr", £("p", "&nbsp;")).

    (!$folder->isRoot()
    ? £("li", £link("dmMediaLibrary/renameFolder?folder_id=".$folder->getId())->text(__("Rename this folder"))->set('.rename_folder.dialog_me.s16.s16_folder_edit'))
	  : "").
    (!$folder->isRoot()
    ? £("li", £link("dmMediaLibrary/moveFolder?folder_id=".$folder->getId())->text(__("Move this folder"))->set('.move_folder.dialog_me.s16.s16_folder_move'))
	  : "").
    (!$folder->isRoot()
    ? £("li", £link("dmMediaLibrary/deleteFolder?folder_id=".$folder->getId())->textTitle(__("Delete this folder"))->set('.delete_folder.confirm_me.s16.s16_folder_delete'))
	  : "")

  )

);