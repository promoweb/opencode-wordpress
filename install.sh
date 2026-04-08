#!/bin/bash

# OpenCode WordPress Installation Script
# Installs OpenCode WordPress skills, agents, rules, and commands

set -e

echo "================================"
echo "OpenCode WordPress Installer"
echo "================================"
echo ""

# Colors for output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Detect OpenCode config directory
OPENCODE_CONFIG_DIR="${HOME}/.opencode"

if [ ! -d "$OPENCODE_CONFIG_DIR" ]; then
    echo -e "${YELLOW}Warning: OpenCode config directory not found at $OPENCODE_CONFIG_DIR${NC}"
    echo "Creating OpenCode config directory..."
    mkdir -p "$OPENCODE_CONFIG_DIR"
fi

# Installation options
INSTALL_MODE="${1:-full}"

echo "Installation mode: $INSTALL_MODE"
echo ""

# Function to install skills
install_skills() {
    echo -e "${BLUE}Installing skills...${NC}"
    
    SKILLS_DIR="$OPENCODE_CONFIG_DIR/skills"
    mkdir -p "$SKILLS_DIR"
    
    # Copy all skills
    for skill_dir in skills/*/; do
        skill_name=$(basename "$skill_dir")
        echo "  - Installing skill: $skill_name"
        cp -r "$skill_dir" "$SKILLS_DIR/$skill_name"
    done
    
    echo -e "${GREEN}✓ Skills installed${NC}"
    echo ""
}

# Function to install rules
install_rules() {
    echo -e "${BLUE}Installing rules...${NC}"
    
    RULES_DIR="$OPENCODE_CONFIG_DIR/rules"
    mkdir -p "$RULES_DIR"
    
    # Copy common rules (always required)
    echo "  - Installing common rules"
    cp -r rules/common "$RULES_DIR/common"
    
    # Copy WordPress rules
    echo "  - Installing WordPress rules"
    cp -r rules/wordpress "$RULES_DIR/wordpress"
    
    echo -e "${GREEN}✓ Rules installed${NC}"
    echo ""
}

# Function to install agents
install_agents() {
    echo -e "${BLUE}Installing agents...${NC}"
    
    AGENTS_DIR="$OPENCODE_CONFIG_DIR/agents"
    mkdir -p "$AGENTS_DIR"
    
    # Copy all agents
    for agent_file in agents/*.md; do
        agent_name=$(basename "$agent_file")
        echo "  - Installing agent: $agent_name"
        cp "$agent_file" "$AGENTS_DIR/$agent_name"
    done
    
    echo -e "${GREEN}✓ Agents installed${NC}"
    echo ""
}

# Function to install hooks
install_hooks() {
    echo -e "${BLUE}Installing hooks...${NC}"
    
    HOOKS_DIR="$OPENCODE_CONFIG_DIR/hooks"
    mkdir -p "$HOOKS_DIR"
    
    # Copy all hooks
    for hook_file in hooks/*.js; do
        hook_name=$(basename "$hook_file")
        echo "  - Installing hook: $hook_name"
        cp "$hook_file" "$HOOKS_DIR/$hook_name"
    done
    
    echo -e "${GREEN}✓ Hooks installed${NC}"
    echo ""
}

# Function to install opencode config
install_config() {
    echo -e "${BLUE}Installing OpenCode configuration...${NC}"
    
    # Check if opencode.json exists in user's config
    if [ -f "$OPENCODE_CONFIG_DIR/opencode.json" ]; then
        echo -e "${YELLOW}Warning: Existing opencode.json found${NC}"
        echo "Backing up existing config..."
        mv "$OPENCODE_CONFIG_DIR/opencode.json" "$OPENCODE_CONFIG_DIR/opencode.json.backup"
    fi
    
    # Copy opencode.json
    cp .opencode/opencode.json "$OPENCODE_CONFIG_DIR/opencode.json"
    
    # Copy commands
    COMMANDS_DIR="$OPENCODE_CONFIG_DIR/commands"
    mkdir -p "$COMMANDS_DIR"
    cp -r .opencode/commands/* "$COMMANDS_DIR/"
    
    # Copy instructions
    INSTRUCTIONS_DIR="$OPENCODE_CONFIG_DIR/instructions"
    mkdir -p "$INSTRUCTIONS_DIR"
    cp -r .opencode/instructions/* "$INSTRUCTIONS_DIR/"
    
    # Copy prompts
    PROMPTS_DIR="$OPENCODE_CONFIG_DIR/prompts"
    mkdir -p "$PROMPTS_DIR"
    cp -r .opencode/prompts/* "$PROMPTS_DIR/"
    
    echo -e "${GREEN}✓ OpenCode configuration installed${NC}"
    echo ""
}

# Execute installation based on mode
case "$INSTALL_MODE" in
    full)
        install_skills
        install_rules
        install_agents
        install_hooks
        install_config
        ;;
    skills)
        install_skills
        ;;
    rules)
        install_rules
        ;;
    agents)
        install_agents
        ;;
    minimal)
        install_rules
        install_config
        ;;
    *)
        echo "Unknown installation mode: $INSTALL_MODE"
        echo "Available modes: full, skills, rules, agents, minimal"
        exit 1
        ;;
esac

# Function to verify installation
verify_installation() {
    echo -e "${BLUE}Verifying installation...${NC}"
    
    ERRORS=0
    
    # Check required directories
    for dir in skills agents rules prompts/agents commands instructions; do
        if [ ! -d "$OPENCODE_CONFIG_DIR/$dir" ]; then
            echo -e "${YELLOW}Warning: Directory $dir not found${NC}"
            ERRORS=$((ERRORS + 1))
        fi
    done
    
    # Check required files
    for file in opencode.json prompts/agents/wordpress-reviewer.txt commands/wp-theme.md; do
        if [ ! -f "$OPENCODE_CONFIG_DIR/$file" ]; then
            echo -e "${YELLOW}Warning: File $file not found${NC}"
            ERRORS=$((ERRORS + 1))
        fi
    done
    
    # Check for old incorrect paths in opencode.json
    if grep -q '{file:.opencode/' "$OPENCODE_CONFIG_DIR/opencode.json" 2>/dev/null; then
        echo -e "${YELLOW}Warning: Found old paths with .opencode/ prefix in opencode.json${NC}"
        echo "  Run: sed -i 's|{file:.opencode/|{file:|g' $OPENCODE_CONFIG_DIR/opencode.json"
        ERRORS=$((ERRORS + 1))
    fi
    
    if [ $ERRORS -eq 0 ]; then
        echo -e "${GREEN}✓ Installation verified successfully${NC}"
    else
        echo -e "${YELLOW}Found $ERRORS issues. Please review the warnings above.${NC}"
    fi
    echo ""
}

echo "================================"
echo -e "${GREEN}Installation Complete!${NC}"
echo "================================"
echo ""

# Verify installation
verify_installation

echo "Next steps:"
echo "1. Restart OpenCode CLI"
echo "2. Try a command: opencode /wp-theme 'Create a theme'"
echo "3. Review documentation in docs/ directory"
echo ""
echo "For more information, see docs/USAGE.md"
echo ""