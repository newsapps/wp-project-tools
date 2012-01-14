#!/usr/bin/env python
# -*- coding: utf-8 -*-
TEMPLATE_DIR="_templates"
TEMPLATE_JS="_templates.js"
TEMPLATE_VAR="TEMPLATES"
TEMPLATE_EXT=".html"

import os, os.path, json
from optparse import OptionParser


parser = OptionParser()
parser.add_option("-v", "--var",
                action='store', type='string', dest='var')
parser.add_option("-w", "--watch",
                action='store_true', dest='watch')


def run(path=False, isrec=False):
    templates = dict()

    template_dir_path = os.path.abspath(TEMPLATE_DIR)
    template_js_path = os.path.abspath(TEMPLATE_JS)

    if isrec:
        raise NotImplementedError

    if path and not path.startswith(template_dir_path):
        print(path)
        print(TEMPLATE_DIR)
        raise RuntimeError

    if os.path.isdir(template_dir_path):
        files = os.listdir(template_dir_path)
        for fname in files:
            tmpl_name, ext = os.path.splitext(fname)
            if ext != TEMPLATE_EXT: continue

            f = open(os.path.join(template_dir_path,fname),'U')
            templates[tmpl_name] = f.read().replace("\n","").replace("\r","")
            f.close()

        tmpl_count = len(templates)
        if tmpl_count < 1:
            print '\033[1;31mCreate some %s templates in the directory called %s\033[1;m' % (TEMPLATE_EXT, TEMPLATE_DIR)
            exit()
            
        tmpl_f = open(template_js_path,'w')
        
        tmpl_f.write("var %s = " % TEMPLATE_VAR)
        json.dump(templates, tmpl_f)
        tmpl_f.close()
        print "«\t\033[1;32mMashed %i template%s into %s\033[1;m" % (tmpl_count, "s" if tmpl_count != 1 else "", TEMPLATE_JS)
    else:
        print '\033[1;31mCreate a directory called %s and put stuff in it\033[1;m' % TEMPLATE_DIR
    
if __name__ == '__main__':
    (opts, args) = parser.parse_args()
    if len(args) != 1 or len(args[0].split(':')) != 2:
        print 'Usage: $ masher templates:template.js\n'
        exit()

    (TEMPLATE_DIR, TEMPLATE_JS) = args[0].split(':')

    TEMPLATE_DIR = TEMPLATE_DIR
    TEMPLATE_JS = TEMPLATE_JS

    # Are we redefining the javascript var?
    if opts.var:
        TEMPLATE_VAR = opts.var

    # If we got this far, run the masher at least once
    run()

    # Do we want to watch the directory for changes?
    if opts.watch:

        # Implemented for Mac OS X
        from platform import system
        if system() != "Darwin":
            raise NotImplementedError("Watching not supported on %s" % system())

        # Try to import pyfsevents
        try:
            import pyfsevents

            # Okay, register the event and enter the loop
            print('»\tWatching %s' % TEMPLATE_DIR)
            pyfsevents.registerpath(TEMPLATE_DIR, run)
            pyfsevents.listen()
        except ImportError:
            print '\033[1;31mInstall pyfsevents http://bitbucket.org/nicdumz/fsevents\033[1;m'
            exit()
