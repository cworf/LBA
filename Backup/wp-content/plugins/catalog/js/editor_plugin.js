(function() {
    tinymce.create('tinymce.plugins.Spider_Catalog_mce', {
 
        init : function(ed, url){
			
			ed.addCommand('mceSpider_Catalog_mce', function() {
				ed.windowManager.open({
					file : spider_ajax+'?action=spidercatalogwindow',
					width : 400 + ed.getLang('Spider_Catalog_mce.delta_width', 0),
					height : 240 + ed.getLang('Spider_Catalog_mce.delta_height', 0),
					inline : 1
				}, {
					plugin_url : url // Plugin absolute URL
				});
			});
            ed.addButton('Spider_Catalog_mce', {
            title : 'Insert Spider Catalog',
            cmd : 'mceSpider_Catalog_mce',
            image: wd_cat_plugin_url + '/images/Spider_CatalogLogoHover.png'
            });
        }
    });
 
    tinymce.PluginManager.add('Spider_Catalog_mce', tinymce.plugins.Spider_Catalog_mce);
 
})();