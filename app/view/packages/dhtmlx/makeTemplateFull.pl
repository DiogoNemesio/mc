#!/usr/bin/perl

$orig	= $ARGV[0];
$dest	= $ARGV[1];

# Checar se o diretório de origem existe
if (!-d $orig) {
	print "$orig: diretório não existe\n";
	exit 0;
}

if (-d $dest) {
	print "Destino já existe: $dest\n";
	exit 0;
}

if (!$dest) {
	print "Destino inválido\n";
	exit 0;
}

if (!$orig) {
	print "Origem inválida\n";
	exit 0;
}

print "Gerando template: $dest\n";

system("cp -af $orig $dest");

# Remove os diretórios svn do destino
print "Remove svn do destino: ";
system("find $dest -name .svn -print0 | xargs -0 rm -rf");
print "OK\n";


# Removendo arquivos desnecessários 
system ("rm -f $dest/*.zip");
system ("rm -f $dest/index.html");
system ("rm -f $dest/readme.txt");
system ("rm -f $dest/dhtmlxgrid_pgn.js.3.0");
system ("rm -f $dest/*.css");
system ("rm -f $dest/*.js");
system ("rm -rf $dest/docsExplorer");
system ("rm -rf $dest/libCompiler");
system ("rm -rf $dest/visualDesigner");
system ("rm -rf $dest/dhtmlxForm");
system ("rm -rf $dest/skins");

# lista os diretórios 
chop(@dirs	= `ls $dest`);

for $dir (@dirs) {
	if (-d "$dest/$dir") {
		print "Verificando diretório $dir: ";
		if (-d "$dest/$dir/codebase") {
			# Apaga os diretórios não necessários 
			system ("rm -rf $dest/$dir/samples");
			system ("rm -rf $dest/$dir/dhtmlx_skin_Black_Toolbar");
			system ("rm -rf $dest/$dir/sources");
			system ("rm -rf $dest/$dir/readme.txt");
		}
		print "OK\n";
	}elsif (-e "$dest/$dir") {
		print "Verificando arquivo $dir: ";
		print "OK\n";
	}

}

exit 0;
