dmTagPlugin
=================

The `dmTagPlugin` allows to add tags to your records.
It packages
- a Doctrine behaviour, "DmTaggable"
- two front widgets: "dmTag/list" and "dmTag/show"
- an admin interface to manage tags

DmTaggable works like [Doctrine Taggable](http://www.doctrine-project.org/extension/Taggable/1_2-1_0).
The main difference is that the DmTag model physically exists.
It allows to use the DmTagTable at any moment.
One Diem page is created for each tag.

The plugin is fully extensible. Only works with [Diem 5.0](http://diem-project.org/) installed.

Documentation
-------------

See the online documentation : [Diem Tag plugin documentation](http://diem-project.org/plugins/dmtagplugin)