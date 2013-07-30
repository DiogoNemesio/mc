#!/usr/bin/perl


$skinFile	= $ARGV[0];
$skinDir	= $ARGV[1];

$tmpDir		= "/tmp/";

if ((!$skinFile) || (!$skinDir)) {
	print "use: ./applySkinFull.pl SKIN_FILE DIR_SKIN\n";
	exit 1;
}

# Checar se o diretório existe
if (!-d $skinDir) {
	print "$skinDir: diretório não existe\n";
	exit 0;
}

if (!-e $skinFile) {
	print "Skin: $skinFile não encontrado!\n";
	exit 0;
}

# Corrigir o css customizado
#system("unzip -o -q -d $tmpDir $skinFile dhtmlx_custom.css");
#system("head -569 $tmpDir/dhtmlx_custom.css > $tmpDir/dhtmlx_custom.css.tmp");
#system("mv $tmpDir/dhtmlx_custom.css.tmp $tmpDir/dhtmlx_custom.css");
#system("zip -r -j $skinFile $tmpDir/dhtmlx_custom.css");
#system("rm -f $tmpDir/dhtmlx_custom.css");

print "Aplicando skin: $skinFile em $skinDir\n";

if (-d "$skinDir") {
	# Descompacta o zip dentro do codebase
	system("unzip -o -q -d $skinDir/ $skinFile ");
	print "OK\n";
}else{
	print "Diretório não encontrado !!!\n";
}

exit 0;
