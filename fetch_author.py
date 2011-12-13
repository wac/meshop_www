#!/usr/bin/env python
# enable debugging

import cgitb
cgitb.enable()

print "Content-Type: text/html"
print

import cgi

form=cgi.FieldStorage()

bitmask = int("1"*1000, 2)
sep='|'


if "name" not in form:
    print "<h1>Error</h1>"
    print "Please supply a name to look up"
    exit()

# Given a hash
# Find the best matches - output the number of bits that match

bctable={}
bitmask16=int("1"*16,2)

def bitcount16(v):
    if v in bctable:
        return bctable[v]

    c = 0
    vi=v
    while vi:
        vi &= vi - 1;
        c += 1;

    bctable[v]=c
    return c

def bitcount(v):
    c=0
    while v:
        c += bitcount16(v & bitmask16)
        v = v >> 16
    return c


numresults=2000

if "limit" in form:
    numresults = int(form["limit"].value)

teststr=form["name"].value.strip().upper()
testname=''
testname2=False

totalhash = {}
totalhash0 = {}
totalhashU = {}

def update_totalhash(key, val, testhash):
    count0=bitcount(val & testhash)
    countU=bitcount(val | testhash)
    count=bitcount((~val ^ testhash) & bitmask)
    if key in totalhash:
        totalhash[key] += count
        totalhash0[key] += count0
        totalhashU[key] += countU
    else:
        totalhash[key] = count
        totalhash0[key] = count0
        totalhashU[key] = countU


hashfiles= []

if 'db' in form:
    hashfiles=["DATA/author-min20-max1000-" + form["db"].value ]
else:
    hashfiles=["DATA/author-min20-max1000-hash0.5-1000.txt"] 

for f in hashfiles:
    h={}
    testhash=0

    for line in open(f):
        tuple=line.strip().split(sep)
        if line[0]=='#':
            print line
            continue
        key=tuple[0]
        val=int(tuple[2])
        h[key]=int(val)
#        if key.startswith(teststr):
#            testhash=val
#            testname=key

    authlist = [ a for a in h if a.startswith(teststr) ]
    if len(authlist) == 0:
        print "<p>No matching authors"
        exit()
    elif len(authlist) == 1:
        testname=authlist[0]
        testhash=h[testname]
    else:
        print "<P><b>Authors Matched</b>:<ul>"
        for a in sorted(authlist):
            print "<li><a href=\"javascript:document.getElementById('auth_name').value='" + a + "'; void(0);\">" + a + "</a>"
        print "</ul>"
        exit()

    print "<p><b>Author Matched</b>:", testname, "<b>Testhash</b>:", testhash

    if ('auth_name2' in form) and (form['auth_name2'].value.strip() != ""):
        auth_name2 = form['auth_name2'].value.strip().upper()
        key=""
        val=""
        authlist = [ a for a in h if a.startswith(auth_name2) ]
        if len(authlist) == 0:
            print "<p>Second Author: No match"
            exit()
        elif len(authlist) == 1:
            key=authlist[0]
            val=h[key]
        else:
            print "<P><b>Authors Matched</b>:<ul>"
            for a in sorted(authlist):
                print "<li><a href=\"javascript:document.getElementById('auth_name2').value='" + a + "'; void(0);\">" + a + "</a>"
            print "</ul>"
            exit()
        print "<p><b>Second Author Matched</b>:", key, "<b>Testhash</b>:", val

        update_totalhash(key, val, testhash)
    else:
        for key,val in h.iteritems():
            update_totalhash(key, val, testhash)

# Need to put the setbits into something compact
def get_setbits(hash, p):
    t=hash
    matches=[]
    i=0

    while(t): 
        i=i-1
        if (t & 1):
            matches.append(p[i])
        t = t >> 1

    print "<select>"
    print "<option> --- "+str(len(matches))+" bits --- </option>"

    for i in sorted(matches):
        print "<option>"+i+"</option>"

    print "</select>"


hashbit_profile = []

profile_file=False

if 'db' in form:
    profile_file=open("DATA/"+form["db"].value)
else:
    profile_file=open("DATA/hash0.5-1000.txt")

for line in profile_file:
    if line=="" or line[0]=='#':
        continue
    tuple = line.split(sep)
    hashbit_profile.append(tuple[0]+" (p<"+str(tuple[1])[0:4]+")")

print "<table class='result-table'><tr><th>Rank</th><th>Name</th><th>Bits Matched</th><th>Sig. Bits Matched</th><th>Combined Score</th><th>Union of Sig. Bits</th><th>Jaccard Index</th></tr>"
i=0

keyfunc_allbits=lambda (k,v): -v
keyfunc_onebits=lambda (k,v): -totalhash0[k]
keyfunc_score=lambda (k,v): (-v*totalhash0[k],k)
keyfunc_jaccard=lambda (k,v): -1.0*totalhash0[k]/totalhashU[k]

keyfunc=keyfunc_score

if 'sortid' in form:
    if form['sortid'].value == "allbits":
        keyfunc=keyfunc_allbits
    elif form['sortid'].value == "onebits":
        keyfunc=keyfunc_onebits
    elif form['sortid'].value == "jaccard":
        keyfunc=keyfunc_jaccard

for key, val in sorted(totalhash.iteritems(), key=keyfunc):
    i=i+1
    print "<tr><td>", i, "</td><td>",
    print "<a href='http://www.ncbi.nlm.nih.gov/pubmed?term="+( key[0:key.rfind(" (")].replace(",","")+"[auth]' target='_blank'>") ,key, "</a><br>", 
    get_setbits(h[key], hashbit_profile)
    print "</td><td>", str(val), "</td><td>", totalhash0[key], "<br>", 
    get_setbits((testhash & h[key]), hashbit_profile)
    print "</td><td>", str(val*totalhash0[key]),
    print "</td><td>", totalhashU[key], 
    print "</td><td>", 1.0*totalhash0[key]/totalhashU[key], "</td></tr>"
    if i >= numresults:
        exit()

