FROM librenms/librenms:latest

USER root

# NMS-WHITE branding
COPY branding/images/ /opt/librenms/html/images/custom/
COPY branding/css/ /opt/librenms/html/css/custom/

# Override browser favicon
COPY branding/images/favicon-16x16.png /opt/librenms/html/images/favicon-16x16.png
COPY branding/images/favicon-32x32.png /opt/librenms/html/images/favicon-32x32.png

# Keep ownership consistent
RUN chown -R librenms:librenms \
    /opt/librenms/html/images/custom \
    /opt/librenms/html/css/custom \
    /opt/librenms/html/images/favicon-16x16.png \
    /opt/librenms/html/images/favicon-32x32.png

USER librenms
