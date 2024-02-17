export default defineAppConfig({
  ui: {
    primary: 'indigo',
    notifications: {
      position: 'top-0 bottom-auto',
    },
    icons: {
      dark: 'i-ph-moon-fill',
      light: 'i-ph-sun-fill',
      system: 'i-ph-desktop-light',
      search: 'i-ph-magnifying-glass-light',
      external: 'i-ph-arrow-up-right-light',
      chevron: 'i-ph-caret-down-light',
      hash: 'i-ph-hash',
    },
    button: {
      default: {
        loadingIcon: 'i-ph-circle-notch',
      },
    },
    input: {
      default: {
        loadingIcon: 'i-ph-circle-notch',
      },
    },
    select: {
      default: {
        loadingIcon: 'i-ph-circle-notch',
        trailingIcon: 'i-ph-caret-down-light',
      },
    },
    selectMenu: {
      default: {
        selectedIcon: 'i-ph-check-light',
      },
    },
    notification: {
      default: {
        closeButton: {
          icon: 'i-ph-x',
        },
      },
    },
    commandPalette: {
      default: {
        icon: 'i-ph-magnifying-glass-light',
        loadingIcon: 'i-ph-circle-notch',
        selectedIcon: 'i-ph-check-light',
        emptyState: {
          icon: 'i-ph-magnifying-glass-light',
        },
      },
    },
    table: {
      default: {
        sortAscIcon: 'i-ph-sort-ascending-light',
        sortDescIcon: 'i-ph-sort-descending-light',
        sortButton: {
          icon: 'i-ph-arrows-down-up-light',
        },
        loadingState: {
          icon: 'i-ph-circle-notch',
        },
        emptyState: {
          icon: 'i-ph-database',
        },
      },
    },
    pagination: {
      default: {
        firstButton: {
          icon: 'i-ph-caret-left',
        },
        prevButton: {
          icon: 'i-ph-arrow-left',
        },
        nextButton: {
          icon: 'i-ph-arrow-right',
        },
        lastButton: {
          icon: 'i-ph-caret-right',
        },
      },
    },
    accordion: {
      default: {
        openIcon: 'i-ph-caret-down-light',
      },
    },
    breadcrumb: {
      default: {
        divider: 'i-ph-caret-right-light',
      },
    },
  },
});
