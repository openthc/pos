#
# OpenTHC API Makefile
#

#
# Help, the default target
help:
	@echo
	@echo "You must supply a make command"
	@echo
	@grep --null-data --only-matching --perl-regexp "#\n#.*\n[\w\-]+:.*\n" $(MAKEFILE_LIST) \
		| awk '/[a-zA-Z0-9_-]+:/ { printf "  \033[0;49;32m%-20s\033[0m%s\n", $$1, gensub(/^# /, "", 1, x) }; { x=$$0 }' \
		| sort
	@echo


#
# Install necessary stuff
install:

	test "$(USER)" = "root"

	apt-get -qy update
	apt-get -qy upgrade
	apt-get -qy install doxygen graphviz

	gem install asciidoctor
	gem install asciidoctor-diagram coderay pygments.rb
	gem install asciidoctor-pdf --pre


#
# All the things
all: docs


#
# Build a bunch of docs
docs: docs-asciidoc docs-doxygen


#
# Generate asciidoc formats
docs-asciidoc:

	mkdir -p ./webroot/doc

	asciidoctor \
		--verbose \
		--backend=html5 \
		--require=asciidoctor-diagram \
		--section-numbers \
		--out-file=./webroot/doc/index.html \
		./doc/index.ad


#
# Generate Doxygent stuff
docs-doxygen:

	doxygen etc/Doxyfile
