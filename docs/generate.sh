#!/bin/bash

THIS_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )";
SOURCE_DIR="$1";

if [[ ! -d $SOURCE_DIR ]]; then
    echo "Directory not provided";
    exit;
fi

shopt -s globstar;

rm -rf "$THIS_DIR/json";

for FILE in ${SOURCE_DIR}**/*.js; do
    if [[ ! -f "$FILE" ]]; then
        continue
    fi

    NEW_FILE=$(echo "$FILE" | sed -e "s~$SOURCE_DIR~~g");
    NEW_FILE=$(echo "$NEW_FILE" | sed -e "s~.js~.json~g");
   
    NEW_FILE_NAME="$(basename $NEW_FILE)";
    
    if 
        [ "$NEW_FILE_NAME" == "index.json" ] ||
        [ "$NEW_FILE_NAME" == "dashboard.json" ] ||
        [ "$NEW_FILE_NAME" == "demo.json" ] ||
        [ "$NEW_FILE_NAME" == "environment.json" ] ||
        [ "$NEW_FILE_NAME" == "content.json" ] || 
        [ "$NEW_FILE_NAME" == "connections.json" ] || 
        [ "$NEW_FILE_NAME" == "forms.json" ] || 
        [ "$NEW_FILE_NAME" == "media.json" ] || 
        [ "$NEW_FILE_NAME" == "schemas.json" ] || 
        [ "$NEW_FILE_NAME" == "utilities.json" ]
    then
        continue
    fi

    echo "$NEW_FILE_NAME";

    mkdir -p "$THIS_DIR/json/$(dirname "$NEW_FILE")";
    jsdoc -X "$FILE" > "$THIS_DIR/json/$NEW_FILE";
done

