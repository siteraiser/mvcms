README~!
Head to getpit.com (subject to change) for the full documentation.

MVCMS is a custom mvc framework augmented for the cms which is built using the mvc system. 

MVCMS is licensed under the GNU open source liscense.

This CMS is designed for developers who are familiar with mvc, it is not recommended for individuals with no web development experience!

Limitations

Only pdo databases are supported at this point, there is no abstraction layer.

The site search only supports the English language due to the need for a different stemming solution per language.

Passwords are stored in plain text by default, this should be remedied by a security expert near you.


Highlights

The admin system is mobile compatible. 

Ckeditor is used for page content and blog articles.

User system has 3 levels; public, author and admin which can be used anywhere being that is an auto loaded class.

Easy deployment requires an empty db and for the files to be unzipped to your public htdocs folder, the tables are setup automatically during the installation process.

Multiple menus can be setup and placed throughout your site independently. The template model can be loaded and used from within a mvc custom app which allows the menus to be uniform and updated throughout where ever they are placed.

 

Developer Notes

The micro mvc can be used independently of the cms and pages, and or content tables can be used by an mvc app so the system can index its content in the site search.

To develop an mvc application to be indexed in the site search you have 2 options.

1. Use the page table and supply an articleid and article with content and type (headline will be the title in the site search, content- searchable exact matches currently highlighed in results). In this case the sitemap and search can be used (.0 priority can be used to remove item from the sitemap).  

2. Use an article only. Columns required are: articlename, published, type and content columns. (description is also used if supplied)
