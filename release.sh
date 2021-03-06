#!/bin/sh

SVN="https://phpwt.googlecode.com/svn/"

REV="`svn info | grep -o 'Revision: .*$' | sed 's/Revision: //'`"
DATE="`svn info | grep -o 'Last Changed Date: *....-..-..' | sed 's/Last Changed Date: *//'`"

VERSION="`cat __VERSION__`"
BUILD="$VERSION build($REV)"
RELEASE="$VERSION"
SOFTWARE="phpwt-$RELEASE"

echo "Building release $BUILD from $DATE"

echo "Creating SVN tag for $SOFTWARE"
svn copy -m "Release for $SOFTWARE" $SVN/trunk $SVN/tags/$SOFTWARE > /dev/null

echo "Creating archive $SOFTWARE.tar.gz"
TMPDIR=`mktemp -d /tmp/$SOFTWARE.XXXXXX`
svn export $SVN/tags/$SOFTWARE $TMPDIR/$SOFTWARE/ > /dev/null

pushd $TMPDIR > /dev/null
rm -Rf $SOFTWARE/scripts
mv $SOFTWARE/__VERSION__ $SOFTWARE/VERSION
tar -zcf $SOFTWARE.tar.gz $SOFTWARE
popd > /dev/null

mv $TMPDIR/$SOFTWARE.tar.gz ~

rm -Rf $TMPDIR
