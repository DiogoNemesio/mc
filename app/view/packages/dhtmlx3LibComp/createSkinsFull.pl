#!/usr/bin/perl


$template	= $ARGV[0];

if (!$template) {
	print "use: ./createSkinsFull.pl DIR_TEMPLATE\n";
	exit 1;
}

# Verifica se o template existe
if (!-d $template) {
	print "$template: template não encontrado\n";
	exit 0;
}

# Procura por arquivos de skin
chop(@skins = `ls skin_*.zip`);

for $skin (@skins) {

	# Verifica se o arquivo está com o nome adequado
	if ($skin =~ m/skin_([a-zA-Z]*)\.zip/) {
		print "Skin: $skin\n";
		print "SkinName = $1\n";
		$skinName  = $1;
		# Verifica se o skin já existe, se existir exclui
		if (-d $skinName) {
			# Remove diretório do skin antigo
			print "Removendo diretório $skinName\n";
			system ("svn delete $skinName");
			system ("svn commit");
			system ("rm -rf $skinName");
		}
		# Copia o template
		system ("cp -af $template $skinName");

		# Aplica o skin
		system ("./applySkinFull.pl $skin $skinName");
		#system ("svn add $skinName");
	}
}

exit 0;
