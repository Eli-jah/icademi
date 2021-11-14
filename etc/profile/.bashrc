# .bashrc

# User specific aliases and functions

# Source global definitions
if [ -f /etc/bashrc ]; then
	. /etc/bashrc
fi

# customized:

# alias mysql=mycli
alias node=nodejs
alias ll="ls -ahl"
alias lg="ls -ahl | grep"
alias lc="ls -ahl | wc -l"
alias sudowww="sudo -H -u www sh -c"
# or:
# alias sudowww="sudo -H -u www bash -c"